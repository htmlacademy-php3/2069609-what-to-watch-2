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
        if ($user->id === $comment->user_id){
            return true;
        }
        return false;
    }

    /**
     * Метод проверяющий является ли юзер модератором
     *
     * @param User $user - юзер
     * @return bool
     */
    public static function isUserModerator(User $user): bool
    {
        if ($user->is_moderator == 1) {
            return true;
        }
        return false;
    }
}
