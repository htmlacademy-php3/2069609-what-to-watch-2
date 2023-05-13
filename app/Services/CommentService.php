<?php

namespace App\Services;

use App\Http\Requests\Comment\CommentAddRequest;
use App\Models\Comment;
use App\Models\Film;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CommentService
{
    private Comment $comment;

    /**
     * Метод получения списка комментариев
     *
     * @param int $idFilm - id фильма для которого нужно вывести список комментариев
     * @return Collection
     */
    public function getCommentsForFilm(int $idFilm): Collection
    {
        return DB::table('users')
            ->join('comments', 'users.id', '=', 'comments.user_id')
            ->select('users.name', 'comments.text', 'comments.rating', 'comments.created_at')
            ->where('comments.film_id', '=', $idFilm)
            ->get();
    }

    /**
     * Метод создания комментария
     *
     * @param int $idFilm - id фильма, для которого создается комментарий
     * @param CommentAddRequest $request
     * @return array
     */
    public function createCommentForFilm(int $idFilm, CommentAddRequest $request): array
    {
        $film = Film::find($idFilm);

        $film->comments()->create([
            'comment_id' => $request->comment,
            'text' => $request->text,
            'rating' => $request->rating,
            'user_id' => Auth::id(),
        ]);

        return [
            'text' => $request->text,
            'rating' => $request->rating,
        ];
    }

    /**
     * Метод, проверяющий есть ли у комментария потомки
     *
     * @return bool
     */
    public function isCommentHasChildren(): bool
    {
        if (Comment::where('comment_id', '=' , $this->comment->id)->exists()) {
            return true;
        }
        return false;
    }


    /**
     * Метод, удаляющий комментрий
     *
     * @return void
     * @throws Throwable
     */
    public function deleteCommentAndChildren(): void
    {
        DB::beginTransaction();
        try {
            Comment::where('comment_id', '=', $this->comment->id)
                ->orWhere('id', '=', $this->comment->id)
                ->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::warning($e->getMessage());
        }
    }

    /**
     * Метод, устанавливающий текущий комментарий
     *
     * @param Comment $comment - текущий комментарий
     * @return void
     */
    public function setComment(Comment $comment): void
    {
        $this->comment = $comment;
    }
}
