<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    private User $user;

    /**
     * Метод, возвращающий информацию о пользователе
     *
     * @return array
     */
    public function getInfo(): array
    {
        if ($this->user->is_moderator) {
            $role = 'Модератор';
        } else {
            $role = 'Пользователь';
        }

        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'avatar' => $this->user->avatar,
            'role' => $role,
        ];
    }

    /**
     * Метод, устанавливающий текущего пользователя
     *
     * @param User $user - текущий юзер
     * @return void
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

}
