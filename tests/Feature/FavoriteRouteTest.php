<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Film;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FavoriteRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест, проверяющий роут favorite - получение списка избранных фильмов GET
     *
     * @throws Exception
     */
    public function test_get_favorites_list(): void
    {
        // Пользователь не залогинен
        $user = User::factory()->create();
        $film = Film::factory()->create();
        Favorite::factory(['user_id' => $user->id, 'film_id' => $film->id])->create();

        $response = $this->getJson(route('favorite.index'));
        $response->assertUnauthorized();

        // Пользователь залогинен
        $user = Sanctum::actingAs(User::factory()->create());
        $film = Film::factory()->create();
        Favorite::factory(['user_id' => $user->id, 'film_id' => $film->id])->create();

        $response = $this->getJson(route('favorite.index'));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' =>  [
                '*' => [
                    'id',
                    'name',
                    'preview_image'
                ]
            ]
        ]);

        $response->assertJsonFragment([
            'id' => $film-> id,
            'name' => $film->name,
            'preview_image' => $film->preview_image,
        ]);
    }

    /**
     * Тест, проверяющий роут films/{id}/favorite - добавление фильма в избранное POST
     *
     * @throws Exception
     */
    public function test_add_favorite(): void
    {
        // Пользователь не залогинен
        $film = Film::factory()->create();

        $response = $this->postJson(route('favorite.add', $film->id));
        $response->assertUnauthorized();

        // Пользователь залогинен
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $film = Film::factory()->create();

        $response = $this->postJson(route('favorite.add', $film->id));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' =>  [
                'message',
            ]
        ]);
        $response->assertJsonFragment([
            'message' => 'Фильм успешно добавлен в избранное',
        ]);

        // Залогиненный пользователь пытается добавить уже избранный фильм
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $film = Film::factory()->create();
        Favorite::factory(['user_id' => $user->id, 'film_id' => $film->id])->create();

        $response = $this->postJson(route('favorite.add', $film->id));
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'data' =>  [
                'message',
                'errors'
            ]
        ]);
        $response->assertJsonFragment([
            'message' => 'Данный фильм уже у вас в избранном',
        ]);

    }

    /**
     * Тест, проверяющий роут films/{id}/favorite - удаление фильма из избранного DELETE
     *
     * @throws Exception
     */
    public function test_delete_favorite(): void
    {
        // Пользователь не залогинен
        $user = User::factory()->create();
        $film = Film::factory()->create();
        Favorite::factory(['user_id' => $user->id, 'film_id' => $film->id])->create();

        $response = $this->deleteJson(route('favorite.delete', $film->id));
        $response->assertUnauthorized();

        // Пользователь залогинен
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $film = Film::factory()->create();
        Favorite::factory(['user_id' => $user->id, 'film_id' => $film->id])->create();

        $response = $this->deleteJson(route('favorite.add', $film->id));
        $response->assertOk();
        $response->assertJsonStructure([
            'data' =>  [
                'message',
            ]
        ]);
        $response->assertJsonFragment([
            'message' => 'Фильм успешно удален из избранного',
        ]);
    }

}

