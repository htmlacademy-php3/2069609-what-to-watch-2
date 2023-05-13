<?php

namespace App\Services;

use App\Http\Repositories\Interfaces\MovieRepositoryInterface;
use App\Models\Actor;
use App\Models\Film;
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

        $actorsList = $film['Actors'];
        $allActors = explode(", ", $actorsList);
        $actorsAlreadyExist = DB::table('actors')
         ->whereIn('name', $allActors)
         ->pluck('name')
         ->all();

        $genreList = $film['Genre'];
        $allGenres = explode(", ", $genreList);
        $genresAlreadyExist = DB::table('genres')
            ->whereIn('title', $allGenres)
            ->pluck('title')
            ->all();

        $actorsForAdd = array_diff($allActors, $actorsAlreadyExist);
        $genresForAdd = array_diff($allGenres, $genresAlreadyExist);

        DB::beginTransaction();
        try {
            foreach ($actorsForAdd as $actor) {
                Actor::create(['name' => $actor]);
            }
            foreach ($genresForAdd as $genre) {
                Genre::create(['title' => $genre]);
            }

            $allActorsIds = DB::table('actors')
                ->whereIn('name', $allActors)
                ->pluck('id')
                ->all();
            $allGenresIds = DB::table('genres')
                ->whereIn('title', $allActors)
                ->pluck('id')
                ->all();

            $film = Film::create([
                'imdb_id' => $imdbId,
                'status' => 'pending',
                'name' => $film['Title'],
                'description' => $film['Plot'],
                'director' => $film['Director'],
                'run_time' => $runTime,
                'released' => $released,
            ]);

            foreach ($allActorsIds as $actorId) {
                $film->actors()->attach(['actor_id' => $actorId]);
            }
            foreach ($allGenresIds as $genreId) {
                $film->genres()->attach(['genre_id' => $genreId]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::warning($e->getMessage());
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
     * @param array $genreIds - массив из id жанров
     * @return void
     * @throws Throwable
     */
    public function updateGenresForFilm(array $genreIds): void
    {
        DB::beginTransaction();
        try {
            $this->film->genres()->detach();
            foreach ($genreIds as $genreId) {
                $this->film->genres()->attach(['genre_id' => $genreId]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::warning($e->getMessage());
        }
    }

    /**
     * Метод обновляющий актеров фильма
     *
     * @param array $starringIds - массив из id актеров
     * @return void
     * @throws Throwable
     */
    public function updateActorsForFilm(array $starringIds): void
    {
        DB::beginTransaction();
        try {
            $this->film->actors()->detach();
            foreach ($starringIds as $starringId) {
                $this->film->actors()->attach(['actor_id' => $starringId]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::warning($e->getMessage());
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

    /**
     * Метод установки фильма ПРОМО-ФИЛЬМОМ
     *
     * @return string[]
     * @throws Throwable
     */
    public function getNewPromoFilm(): array
    {
        $promo = Film::where(['is_promo' => true])->first();

        DB::beginTransaction();
        try {
            if ($promo) {
                $promo->is_promo = false;
                $promo->save();
            }

            $this->film->is_promo = true;
            $this->film->save();
        } catch (Exception $e) {
            DB::rollback();
            Log::warning($e->getMessage());
        }
        return ['message' => 'Фильм с id = ' . $this->film->id . ' стал новым промо-фильмом'];
    }

}
