<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Film;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CommentRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест, проверяющий роут films/{id}/comments - получение списка отзывов к фильму GET
     *
     * @throws Exception
     */
    public function test_get_comments_list(): void
    {
        // Пользователь не залогинен
        $count = random_int(2, 10);

        $film = Film::factory()
            ->has(Comment::factory($count)
                ->for(User::factory()
                    ->create()))
            ->create();

        $comment = $film->comments->first();

        $response = $this->getJson(route('comments.index', $film->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $comment->user->name,
            'text' => $comment->text,
            'rating' => $comment->rating,
        ]);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'text',
                    'rating',
                    'created_at'
                ]
            ]
        ]);

        // Пользователь залогинен и не модератор
        $count = random_int(2, 10);

        $film = Film::factory()
            ->has(Comment::factory($count)
                ->for(User::factory()
                    ->create()))
            ->create();

        $comment = $film->comments->first();

        Sanctum::actingAs(User::factory()->create());
        $response = $this->getJson(route('comments.index', $film->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $comment->user->name,
            'text' => $comment->text,
            'rating' => $comment->rating,
        ]);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'text',
                    'rating',
                    'created_at'
                ]
            ]
        ]);

        // Пользователь залогинен и модератор
        $count = random_int(2, 10);

        $film = Film::factory()
            ->has(Comment::factory($count)
                ->for(User::factory()
                    ->create()))
            ->create();

        $comment = $film->comments->first();

        Sanctum::actingAs(User::factory()->moderator()->create());
        $response = $this->getJson(route('comments.index', $film->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name' => $comment->user->name,
            'text' => $comment->text,
            'rating' => $comment->rating,
        ]);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'name',
                    'text',
                    'rating',
                    'created_at'
                ]
            ]
        ]);
    }

    /**
     * Тест, проверяющий роут films/{id}/comments - добавление отзыва к фильму POST
     *
     * @throws Exception
     */
    public function test_post_comment(): void
    {
        // Пользователь не авторизован
        $film = Film::factory()->create();
        $testText = 'Test-test-test-test-test-test-test-test-test-test-test-test-test-test-test-test-test';
        $testRating = random_int(1, 10);

        $response = $this->postJson(route('comments.store', $film->id), ['text' => $testText, 'rating' => $testRating]);

        $response->assertUnauthorized();

        // Пользователь авторизован
        $film = Film::factory()->create();
        $filmId = $film->id;

        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('comments.store', $filmId), ['text' => $testText, 'rating' => $testRating]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'text' => $testText,
            'rating' => $testRating,
        ]);
        $response->assertJsonStructure([
            'data' => [
                'text',
                'rating',
            ]
        ]);

        // Пользователь авторизован, оставляет комментарий без рейтинга
        $film = Film::factory()->create();
        $filmId = $film->id;
        $testText = '';
        $testRating = random_int(1, 10);

        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson(route('comments.store', $filmId), ['text' => $testText, 'rating' => $testRating]);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'text',
            ]
        ]);
    }

    /**
     * Тест, проверяющий роут comments/{id} - редактирование комментария PATCH
     *
     * @throws Exception
     */
    public function test_patch_comment(): void
    {
        // Пользователь не залогинен
        $film = Film::factory()
            ->has(Comment::factory()
                ->for(User::factory()
                    ->create()))
            ->create();

        $comment = $film->comments->first();
        $commentId = $comment->id;

        $newCommentText = 'Test-test-test-test-test-test-test-test-test-test-test';
        $newCommentRating = random_int(1, 10);

        $response = $this->patchJson(route('comments.update', $commentId), ['text' => $newCommentText, 'rating' => $newCommentRating]);
        $response->assertUnauthorized();

        // Пользователь  залогиненный не автор и не модератор
        $userNotAuthor = User::factory()->create();
        Sanctum::actingAs($userNotAuthor);

        $userAuthor = User::factory()->create();
        $film = Film::factory()->create();

        $comment = Comment::factory([
            'user_id' => $userAuthor->id,
            'film_id' => $film->id
            ]
        )->create();

        $commentId = $comment->id;

        $response = $this->patchJson(route('comments.update', $commentId), ['text' => $newCommentText, 'rating' => $newCommentRating]);
        $response->assertForbidden();

        // Пользователь залогиненный автор
        $userAuthor = User::factory()->create();
        Sanctum::actingAs($userAuthor);

        $film = Film::factory()->create();

        $comment = Comment::factory([
                'user_id' => $userAuthor->id,
                'film_id' => $film->id
            ]
        )->create();

        $commentId = $comment->id;

        $response = $this->patchJson(route('comments.update', $commentId), ['text' => $newCommentText, 'rating' => $newCommentRating]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'text' => $newCommentText,
            'rating' => $newCommentRating
        ]);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'film_id',
                'text',
                'rating',
                'comment_id',
                'created_at',
                'updated_at'
            ]
        ]);

        // Пользователь залогиненный модератор, не автор
        $userNotAuthor = User::factory()->moderator()->create();
        Sanctum::actingAs($userNotAuthor);

        $userAuthor = User::factory()->create();
        $film = Film::factory()->create();

        $comment = Comment::factory([
                'user_id' => $userAuthor->id,
                'film_id' => $film->id
            ]
        )->create();

        $commentId = $comment->id;

        $response = $this->patchJson(route('comments.update', $commentId), ['text' => $newCommentText, 'rating' => $newCommentRating]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'text' => $newCommentText,
            'rating' => $newCommentRating
        ]);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'user_id',
                'film_id',
                'text',
                'rating',
                'comment_id',
                'created_at',
                'updated_at'
            ]
        ]);
    }

    /**
     * Тест, проверяющий роут comments/{id} - удаление комментария DELETE
     *
     * @throws Exception
     */
    public function test_delete_comment(): void
    {
        // Пользователь не залогинен
        $comment = Comment::factory()->create();
        $commentId = $comment->id;

        $response = $this->deleteJson(route('comments.delete', $commentId));
        $response->assertUnauthorized();

        // Пользователь залогинен и не автор
        Sanctum::actingAs(User::factory()->create());

        $comment = Comment::factory()->create();
        $commentId = $comment->id;

        $response = $this->deleteJson(route('comments.delete', $commentId));
        $response->assertForbidden();

        // Пользователь залогинен, не автор, но модератор
        Sanctum::actingAs(User::factory()->moderator()->create());

        $comment = Comment::factory()->create();
        $commentId = $comment->id;

        $response = $this->deleteJson(route('comments.delete', $commentId));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'message'
            ]
        ]);

        // Пользователь залогинен и автор
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $comment = Comment::factory(['user_id' => $user->id])->create();
        $commentId = $comment->id;

        $response = $this->deleteJson(route('comments.delete', $commentId));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'message'
            ]
        ]);
    }
}

