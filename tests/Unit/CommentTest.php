<?php

namespace Tests\Unit;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /**
     *  Тест, проверяющий метод модели Comment для возврата имени авторизованного автора комментария
     *  Имя пользователя должно совпадать с именем, который возвращает данный метод модели Comment
     */
    public function test_comment_author_name(): void
    {
        $film = Film::factory()->create();
        $user = User::factory()->create();

        $comment = Comment::factory()->create([
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);

        $userName = $user->name;
        $commentAuthor = $comment->user->name;

        $this->assertEquals($userName, $commentAuthor);
    }

    /**
     *  Тест, проверяющий свойство модели Comment для возврата имени анонимного автора комментария
     *  Имя пользователя возвращаемое данным свойством модели Comment должно возвращать строку Guest Author
     */
    public function test_comment_anonym_name(): void
    {
        $film = Film::factory()->create();
        $user = User::factory()->create();

        $comment = Comment::factory()->make([
            'film_id' => $film->id,
            'user_id' => $user->id,
        ]);

        $user->delete();

        $userName = 'Guest Author';
        $commentAuthor = $comment->user->name;

        $this->assertEquals($userName, $commentAuthor);
    }


}
