<?php

namespace App\Handlers;

use App\Models\Comment;
use App\Models\User;

class Permission
{
    /**
     * Метод проверяющий является ли юзер автором комментария
     *
     * @param User $user - юзер
     * @param Comment $comment - комментарий
     * @return bool
     */
    public static function isUserAuthor(User $user, Comment $comment):bool
    {
        return $user->id === $comment->user_id;
    }

    /**
     * Метод проверяющий является ли юзер модератором
     *
     * @param User $user - юзер
     * @return bool
     */
    public static function isUserModerator(User $user): bool
    {
        return $user->is_moderator;
    }
}
