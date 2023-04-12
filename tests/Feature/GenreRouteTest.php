<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\User;
use Database\Factories\GenreFactory;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GenreRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест, проверяющий роут genres/index - получение списка жанров GET
     *
     * @throws Exception
     */
    public function test_get_genres_list(): void
    {
        $count = random_int(2, 10);
        Genre::factory()->count($count)->create();

        // Пользователь незалогинен
        $response = $this->getJson(route('genres.index'));

        $response->assertStatus(200);
        $response->assertJsonCount($count, 'data');
        $response->assertJsonStructure([
            'data' => []
        ]);

        // Пользователь залогинен и не модератор
        Sanctum::actingAs(User::factory()->create());
        $response = $this->getJson(route('genres.index'));

        $response->assertOk();
        $response->assertJsonCount($count, 'data');
        $response->assertJsonStructure([
            'data' => []
        ]);

        // Пользователь залогиненный модератор
        Sanctum::actingAs(User::factory()->moderator()->create());
        $response = $this->getJson(route('genres.index'));

        $response->assertOk();
        $response->assertJsonCount($count, 'data');
        $response->assertJsonStructure([
            'data' => []
        ]);
    }

    /**
     * Тест, проверяющий роут genres/{id} - редактирование жанра
     * @throws Exception
     */
    public function test_patch_genre(): void
    {
        $genre = Genre::create(['title' => 'Test']);
        $genreId = $genre->id;

        $updateGenreTitle = 'Test-test';

        // Пользователь незалогинен
        $response = $this->patchJson(route('genres.update', $genreId), ['title' => $updateGenreTitle]);
        $response->assertUnauthorized();

        // Пользователь залогиненный модератор
        Sanctum::actingAs(User::factory()->moderator()->create());
        $response = $this->patchJson(route('genres.update', $genreId), ['title' => $updateGenreTitle]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => []
        ]);

        // Пользователь залогинен, не модератор
        Sanctum::actingAs(User::factory()->create());

        $response = $this->patchJson(route('genres.update', $genreId), ['title' => $updateGenreTitle]);
        $response->assertForbidden();


    }
}

