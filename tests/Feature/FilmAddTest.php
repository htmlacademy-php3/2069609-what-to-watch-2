<?php

namespace Tests\Feature;

use App\Http\Repositories\Interfaces\MovieRepositoryInterface;
use App\Jobs\AddFilmJob;
use App\Models\Actor;
use App\Models\Film;
use App\Models\Genre;
use App\Services\FilmService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;
use Throwable;

class FilmAddTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверка метода handle класса AddFilmJob по добавлению фильма в базу
     *
     * @return void
     * @throws Throwable
     */
    public function test_add_film(): void
    {
        $film = Film::factory()->make()->toArray();
        $actors = Actor::factory(3)->make();
        $genres = Genre::factory(2)->make();


        $film['Runtime'] = $film['run_time'];
        $film['Released'] = $film['released'];

        $film['Actors'] = [];
        $film['Genre'] = [];

        foreach ($actors as $actor) {
            $film['Actors'][] = $actor->name;
        }
        foreach ($genres as $genre) {
            $film['Genre'][] = $genre->title;
        }

        $film['Genre'] = implode(', ', $film['Genre']);
        $film['Actors'] = implode(', ', $film['Actors']);

        $film['Title'] = $film['name'];
        $film['Plot'] = $film['description'];
        $film['Director'] = $film['director'];

        $repository = $this->mock(MovieRepositoryInterface::class, function (MockInterface $mock) use ($film) {
            $mock->shouldReceive('getMovies')->andReturn($film);
        });
        $filmService = new FilmService($repository);

        $imdbId = $film['imdb_id'];

        $addFilmJob = new AddFilmJob($imdbId);
        $addFilmJob->handle($filmService);

        unset($film['Actors']);
        unset($film['Genre']);
        unset($film['Runtime']);
        unset($film['Released']);
        unset($film['Plot']);
        unset($film['Director']);
        unset($film['Title']);

        $this->assertDatabaseCount('films', 1);
        $this->assertDatabaseCount('actors', 3);
        $this->assertDatabaseCount('genres', 2);

        // Проверка наличия полей в созданных записях
        $filmCreate = Film::first()->toArray();

        foreach ($film as $key => $value) {
            $this->assertArrayHasKey($key, $filmCreate);
        }

        foreach ($actors as $actor) {
            $this->assertDatabaseHas('actors', ['name' => $actor['name']]);
        }

        foreach ($genres as $genre) {
            $this->assertDatabaseHas('genres', ['title' => $genre['title']]);
        }
    }

    /**
     * Проверка метода handle класса AddFilmJob по добавлению фильма в базу
     *
     * @return void
     */
    public function test_add_task_queue(): void
    {
        Queue::fake();

        $imdbId = 'tt0944947';

        AddFilmJob::dispatch($imdbId);

        Queue::assertPushed(AddFilmJob::class);
    }

}
