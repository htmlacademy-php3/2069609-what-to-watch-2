<?php

namespace App\Services;

use App\Http\Repositories\Interfaces\MovieRepositoryInterface;
use App\Http\Requests\Film\FilmUpdateRequest;
use App\Models\Actor;
use App\Models\ActorFilm;
use App\Models\Film;
use App\Models\FilmGenre;
use App\Models\Genre;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class FilmService
{
    private Film $film;
    private MovieRepositoryInterface $repository;

    public function __construct(MovieRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Метод добавления фильма в базу, возвращающий информацию о фильме
     *
     * @param string $imdbId - id добавляемого фильма
     * @return Film
     * @throws Throwable
     */
    public function addFilm(string $imdbId): Film
    {
        $film = $this->repository->getMovies($imdbId);

        $runTime = strtok($film['Runtime'], " ");
        $released = strstr($film['Released'], ' ', true);

        $actorIds = [];
        $actorsList = $film['Actors'];
        $actors = explode(", ", $actorsList);
        foreach ($actors as $actor) {
            $actorIds[] = Actor::firstOrCreate(['name' => $actor])->id;
        }

        $genreIds = [];
        $genreList = $film['Genre'];
        $genres = explode(", ", $genreList);
        foreach ($genres as $genre) {
            $genreIds[] = Genre::firstOrCreate(['title' => $genre])->id;
        }

        $film =  Film::create([
            'imdb_id' => $imdbId,
            'status' => 'pending',
            'name' => $film['Title'],
            'description' => $film['Plot'],
            'director' => $film['Director'],
            'run_time' => $runTime,
            'released' => $released,
        ]);

        foreach ($actorIds as $actorId) {
            ActorFilm::create([
                'actor_id' => $actorId,
                'film_id' => $film->id,
            ]);
        }
        foreach ($genreIds as $genreId) {
            FilmGenre::create([
                'genre_id' => $genreId,
                'film_id' => $film->id,
            ]);
        }
        return $film;
    }

    /**
     * Метод, возвращающий информацию о фильме
     *
     * @param User|null $user - null, если пользователь не авторизован, поле isFavorite не выводится
     * @return array
     */
    public function showInfoAboutFilm(User $user = null): array
    {
        $filmStarring = $this->film->actors->pluck('name');
        $filmGenres = $this->film->genres->pluck('title');
        $filmRating = $this->film->getRating();
        $filmScoresCount = $this->film->comments->count();

        $filmInfo = [
            'id' => $this->film->id,
            'name' => $this->film->name,
            'poster_image' => $this->film->poster_image,
            'preview_image' => $this->film->preview_image,
            'background_image' => $this->film->background_image,
            'background_color' => $this->film->background_color,
            'video_link' => $this->film->video_link,
            'preview_video_link' => $this->film->preview_video_link,
            'description' => $this->film->description,
            'rating' => $filmRating,
            'score_count'=> $filmScoresCount,
            'director' => $this->film->director,
            'starring' => $filmStarring,
            'run_time' => $this->film->run_time,
            'genre' => $filmGenres,
            'released' => $this->film->released,
        ];

        if ($user) {
            if ($user->favorites()->where('film_id', '=', $this->film->id)->first()) {
                $filmInfo['isFavorite'] = 1;
            } else {
                $filmInfo['isFavorite'] = 0;
            }
        }

        return $filmInfo;
    }

    /**
     * Метод обновляющий жанры фильма
     *
     * @param FilmUpdateRequest $request
     * @return void
     * @throws Throwable
     */
    public function updateGenresForFilm(FilmUpdateRequest $request): void
    {
        if ($request->genre_id) {

            DB::beginTransaction();
            try {
                $this->film->genres()->delete();

                foreach ($request->genre_id as $genreId) {
                    FilmGenre::create([
                        'film_id' => $this->film->id,
                        'genre_id' => $genreId
                    ]);
                }
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                Log::warning($e->getMessage());
            }
        }
    }

    /**
     * Метод обновляющий актеров фильма
     *
     * @param FilmUpdateRequest $request
     * @return void
     * @throws Throwable
     */
    public function updateActorsForFilm(FilmUpdateRequest $request): void
    {
        if ($request->starring_id) {

            DB::beginTransaction();
            try {
                $this->film->actors()->delete();
                foreach ($request->starring_id as $starringId) {
                    ActorFilm::create([
                        'film_id' => $this->film->id,
                        'actor_id' => $starringId
                    ]);
                }
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                Log::warning($e->getMessage());
            }
        }
    }

    /**
     * Метод возвращаюший массив из похожих фильмов
     *
     * @return array
     * @throws Throwable
     */
    public function getSimilarFilms(): array
    {

        $genreIds = $this->film->genres->pluck('id')->all();

        $similarFilmsIds = DB::table('film_genre')
            ->whereIn('genre_id', $genreIds)
            ->pluck('film_id')
            ->all();

        $uniqueSimilarFilmsIds = array_unique($similarFilmsIds);

        $idForDelete = array_search($this->film->id, $uniqueSimilarFilmsIds);

        unset($uniqueSimilarFilmsIds[$idForDelete]);
        shuffle($uniqueSimilarFilmsIds);
        $filmIdsForShow = array_slice($uniqueSimilarFilmsIds, 0, 4);

        return DB::table('films')
            ->whereIn('id', $filmIdsForShow)
            ->get()->all();
    }

    /**
     * Метод, устанавливающий текущий фильм
     *
     * @param Film $film - текущий фильм
     * @return void
     */
    public function setFilm(Film $film): void
    {
        $this->film = $film;
    }

}
