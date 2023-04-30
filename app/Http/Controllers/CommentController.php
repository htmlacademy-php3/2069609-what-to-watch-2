<?php

namespace App\Http\Controllers;

use App\Handlers\Permission;
use App\Http\Requests\Comment\CommentAddRequest;
use App\Http\Requests\Comment\CommentUpdateRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\Fail\FailResponse;
use App\Http\Responses\Fail\ForbiddenResponse;
use App\Http\Responses\Fail\NotFoundResponse;
use App\Http\Responses\Success\SuccessResponse;
use App\Models\Comment;
use App\Models\Film;
use App\Services\CommentService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Throwable;

class CommentController extends Controller
{
    private CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Получение списка комментариев к фильму
     *
     * @param int $filmId - id фильма, для которого получаем список комментариев
     * @return BaseResponse
     */
    public function index(int $filmId): BaseResponse
    {
        $film = Film::find($filmId);

        if (!$film) {
            return new NotFoundResponse();
        }

        $comments = $this->commentService->getCommentsForFilm($filmId);

        return new SuccessResponse($comments);
    }


    /**
     * Добавление комментария к фильму
     *
     * @param CommentAddRequest $request
     * @param int $filmId - id фильма, для которого создается новый комментарий
     * @return BaseResponse
     */
    public function store(CommentAddRequest $request, int $filmId): BaseResponse
    {
        try {
            $film = Film::find($filmId);

            if (!$film) {
                return new NotFoundResponse();
            }

            $data = $this->commentService->createCommentForFilm($filmId, $request);

            return new SuccessResponse($data, 201);
        } catch (Exception $e) {
            return new FailResponse(statusCode:500, exception: $e);
        }
    }

    /**
     * Редактирование комментария
     *
     * @param CommentUpdateRequest $request
     * @param int $commentId - id редактируемого комментария
     * @return BaseResponse
     */
    public function update(CommentUpdateRequest $request, int $commentId): BaseResponse
    {
        try {
            $comment = Comment::find($commentId);

            if (!$comment) {
                return new NotFoundResponse();
            }

            $user = Auth::user();

            if ((Permission::isUserAuthor($user, $comment) || Permission::isUserModerator($user)) == 0) {
                return new ForbiddenResponse();
            }

            $comment->update($request->validated());

            return new SuccessResponse($comment->fresh());
        } catch (Exception $e) {
            return new FailResponse(statusCode:500, exception: $e);
        }
    }

    /**
     * Удаление комментария
     *
     * @param int $commentId - id удаляемого комментария
     * @return BaseResponse
     * @throws Throwable
     */
    public function destroy(int $commentId): BaseResponse
    {
        try {
            $comment = Comment::find($commentId);

            if (!$comment) {
                return new NotFoundResponse();
            }

            $user = Auth::user();
            $this->commentService->setComment($comment);

            //Если тек пользователь автор|модератор && у комментария нет потомков
            if ((Permission::isUserAuthor($user, $comment) || Permission::isUserModerator($user)) && !$this->commentService->isCommentHaveChildren()) {
                $comment->delete();
                $messageData = ['message' => 'Комментарий успешно удален'];
                return new SuccessResponse($messageData);
            }

            //Если тек пользователь модератор && у комментария есть потомки
            if (Permission::isUserModerator($user) && $this->commentService->isCommentHaveChildren()) {
                $this->commentService->deleteCommentAndChildren();

                $messageData = ['message' => 'Комментарий  и его потомки успешно удалены'];
                return new SuccessResponse($messageData);
            }

            //Если текущий пользователь автор, но у комментария есть потомки
            if (Permission::isUserAuthor($user, $comment) && $this->commentService->isCommentHaveChildren()) {
                return new ForbiddenResponse(message: 'Вы не можете удалить данный комментарий, тк у него есть ответы.');
            }

            return new ForbiddenResponse();

        } catch (Exception $e) {
            return new FailResponse(statusCode:500, exception: $e);
        }
    }
}
