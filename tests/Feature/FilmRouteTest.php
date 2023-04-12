<?php

namespace Tests\Feature;

use App\Models\Film;
use App\Models\FilmGenre;
use App\Models\Genre;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FilmRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест, проверяющий роут films - получение списка фильмов GET
     *
     *
     * @throws Exception
     */
    public function test_get_films_list(): void
    {
        $count = random_int(5, 10);
        Film::factory()->count($count)->create();

        $response = $this->getJson(route('films.index'));

        $response->assertStatus(200);
        $response->assertJsonFragment(['total' => $count]);
        $response->assertJsonStructure([
            'data' =>  [
                'current_page',
                'data',
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links',
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total'
            ]
        ]);
    }

    /**
     * Тест, проверяющий роут films - добавление фильма в базу POST
     *
     * @throws Exception
     */
    public function test_add_film(): void
    {
        $testImdbId1 = 'tt0116581';

        // Пользователь не залогинен
        $response = $this->postJson(route('films.store'), ['imdb_id' => $testImdbId1]);
        $response->assertUnauthorized();

        // Пользователь залогинен и модератор
        Sanctum::actingAs(User::factory()->moderator()->create());
        $response = $this->postJson(route('films.store'), ['imdb_id' => $testImdbId1]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => []
        ]);

        // Пользователь залогинен, не модератор
        $testImdbId2 = 'tt0120655';

        Sanctum::actingAs(User::factory()->create());
        $response = $this->postJson(route('films.store'), ['imdb_id' => $testImdbId2]);
        $response->assertForbidden();
    }

    /**
     * Тест, проверяющий роут films/{id} - получение информации о фильме GET
     *
     * @throws Exception
     */
    public function test_show_film(): void
    {
        // Пользователь не залогинен
        $film = Film::factory()->create();
        $filmId = $film->id;

        $response = $this->getJson(route('films.show', $filmId));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' =>  [
                'id',
                'name',
                'poster_image',
                'preview_image',
                'background_image',
                'background_color',
                'video_link',
                'preview_video_link',
                'description',
                'rating',
                'score_count',
                'director',
                'starring',
                'run_time',
                'genre',
                'released'
            ]
        ]);
        $response->assertJsonFragment([
            'name' => $film->name,
            'poster_image' => $film->poster_image,
        ]);

        // Пользователь залогинен
        Sanctum::actingAs(User::factory()->create());
        $film = Film::factory()->create();
        $filmId = $film->id;

        $response = $this->getJson(route('films.show', $filmId));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' =>  [
                'id',
                'name',
                'poster_image',
                'preview_image',
                'background_image',
                'background_color',
                'video_link',
                'preview_video_link',
                'description',
                'rating',
                'score_count',
                'director',
                'starring',
                'run_time',
                'genre',
                'released',
                'isFavorite'
            ]
        ]);
        $response->assertJsonFragment([
            'name' => $film->name,
            'poster_image' => $film->poster_image,
        ]);
    }

    /**
     * Тест, проверяющий роут films/{id} - редактирование фильма PATCH
     *
     * @throws Exception
     */
    public function test_update_film(): void
    {
        $newName = 'Test-test';
        $newImdbId = 'tt1234567';
        $newStatus = 'ready';

        // Пользователь не залогинен
        $film = Film::factory()->create();
        $filmId = $film->id;

        $response = $this->patchJson(route('films.update', $filmId), [
            'name' => $newName,
            'imdb_id' => $newImdbId,
            'status' => $newStatus
        ]);
        $response->assertUnauthorized();

        // Пользователь залогинен и не модератор
        Sanctum::actingAs(User::factory()->create());
        $film = Film::factory()->create();
        $filmId = $film->id;

        $response = $this->patchJson(route('films.update', $filmId), [
            'name' => $newName,
            'imdb_id' => $newImdbId,
            'status' => $newStatus
        ]);        $response->assertForbidden();

        // Пользователь залогинен и модератор
        Sanctum::actingAs(User::factory()->moderator()->create());
        $film = Film::factory()->create();
        $filmId = $film->id;

        $response = $this->patchJson(route('films.update', $filmId), [
            'name' => $newName,
            'imdb_id' => $newImdbId,
            'status' => $newStatus
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' =>  [
                'id',
                'name',
                'poster_image',
                'preview_image',
                'background_image',
                'background_color',
                'video_link',
                'preview_video_link',
                'description',
                'rating',
                'score_count',
                'director',
                'starring',
                'run_time',
                'genre',
                'released'
            ]
        ]);
    }

    /**
     * Тест, проверяющий роут films/{id}/similar - получение списка похожих фильмов GET
     *
     * @throws Exception
     */
    public function test_get_similar_films(): void
    {
        Film::factory(10)->create();
        $genre = Genre::factory(['title' => 'test'])->create();

        FilmGenre::factory()
            ->count(20)
            ->state(new Sequence(
                fn ($sequence) => ['film_id' => Film::all()->random(), 'genre_id' => Genre::all()->random()],
            ))
            ->create();

        $film = Film::factory()->create();
        FilmGenre::factory(['film_id' => $film->id, 'genre_id' => $genre->id])->create();
        $response = $this->getJson(route('films.similar', $film->id));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => []
        ]);
    }
}

