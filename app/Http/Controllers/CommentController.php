<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CommentAddRequest;
use App\Http\Requests\Comment\CommentUpdateRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\Fail\FailResponse;
use App\Http\Responses\Fail\ForbiddenResponse;
use App\Http\Responses\Fail\NotFoundResponse;
use App\Http\Responses\Success\SuccessResponse;
use App\Models\Comment;
use App\Models\Film;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Получение списка комментариев к фильму
     * Доступно всем
     *
     * @param $filmId - id фильма, для которого получаем список комментариев
     * @return BaseResponse
     */
    public function index($filmId): BaseResponse
    {
        if (!$filmId) {
            return new NotFoundResponse();
        }

        $film = Film::find($filmId);

        if (!$film) {
            return new NotFoundResponse();
        }

        $comments = DB::table('users')
            ->join('comments', 'users.id', '=', 'comments.user_id')
            ->select('users.name', 'comments.text', 'comments.rating', 'comments.created_at')
            ->where('comments.film_id', '=', $filmId)
            ->get();

        return new SuccessResponse($comments);
    }

    /**
     * Добавление комментария к фильму
     * Доступно только авторизованным пользователям
     *
     * @param CommentAddRequest $request
     * @param $filmId - id фильма, для которого создается новый комментарий
     * @return BaseResponse
     */
    public function store(CommentAddRequest $request, $filmId): BaseResponse
    {
        try {
            if (!$filmId) {
                return new NotFoundResponse();
            }

            $film = Film::find($filmId);

            if (!$film) {
                return new NotFoundResponse();
            }

            $film->comments()->create([
                'comment_id' => $request->comment,
                'text' => $request->text,
                'rating' => $request->rating,
                'user_id' => Auth::id(),
            ]);

            $data = [
                'text' => $request->text,
                'rating' => $request->rating,
            ];
            return new SuccessResponse($data, 201);
        } catch (Exception $e) {
            return new FailResponse(statusCode:500, exception: $e);
        }
    }

    /**
     * Редактирование комментария
     * Доступуно авторизованному автору комментария или модератору
     *
     * @param CommentUpdateRequest $request
     * @param $commentId - id редактируемого комментария
     * @return BaseResponse
     */
    public function update(CommentUpdateRequest $request, $commentId): BaseResponse
    {
        try {
            if (!$commentId) {
                return new NotFoundResponse();
            }

            $comment = Comment::find($commentId);

            if (!$comment) {
                return new NotFoundResponse();
            }

            $user = Auth::user();

            if (($user->id === $comment->user_id || $user->is_moderator) == 0) {
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
     * Доступно авторизованному автору комментария, если у него нет потомков комментариев.
     * И модератору, в этом случае удаляются и потомки.
     *
     * @param $commentId - id удаляемого комментария
     * @return BaseResponse
     */
    public function destroy($commentId): BaseResponse
    {
        try {
            if (!$commentId) {
                return new NotFoundResponse();
            }

            $comment = Comment::find($commentId);

            if (!$comment) {
                return new NotFoundResponse();
            }

            $user = Auth::user();

            $haveChildren = 0;

            if (DB::table('comments')->where('comment_id', '=' , $commentId)->first()) {
                $haveChildren = 1;
            }

            //Если тек пользователь автор|модератор && у комментария нет потомков
            if (($user->id === $comment->user_id || $user->is_moderator) && $haveChildren === 0) {
                $comment->delete();
                $message = ['message' => 'Комментарий успешно удален'];
                return new SuccessResponse($message);
            }

            //Если тек пользователь модератор && у комментария есть потомки
            if (($user->is_moderator) && $haveChildren === 1) {

                DB::delete('delete from comments where comment_id = ?', [$commentId]);
                $comment->delete();

                $message = ['message' => 'Комментарий  и его потомки успешно удалены'];
                return new SuccessResponse($message);
            }

            //Если текущий пользователь автор, но у комментария есть потомки
            if ($user->id === $comment->user_id && $haveChildren !== 0) {
                return new ForbiddenResponse(message: 'Вы не можете удалить данный комментарий, тк у него есть ответы.');
            }

            return new ForbiddenResponse();

        } catch (Exception $e) {
            return new FailResponse(statusCode:500, exception: $e);
        }
    }
}
