<?php

namespace App\Services;

use App\Http\Repositories\AddFilmFromRepository;
use App\Http\Repositories\OmdbRepository;
use App\Http\Requests\Film\FilmUpdateRequest;
use App\Models\ActorFilm;
use App\Models\Film;
use App\Models\FilmGenre;
use App\Models\User;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class FilmService
{
    private Film|null $film;

    public function __construct(Film $film = null)
    {
        $this->film = $film;
    }

    /**
     * Метод добавления фильма в базу, возвращающий информацию о фильме
     *
     * @param string $imdbId - id добавляемого фильма
     * @return Film
     * @throws GuzzleException
     * @throws Throwable
     */
    public function addFilm(string $imdbId): Film
    {
        $client = new Client();
        $omdbRepository = new OmdbRepository($client);
        $omdbFilm = new AddFilmFromRepository($omdbRepository, $imdbId);

        $film = $omdbFilm->getFilmInfo();

        $runTime = strtok($film['Runtime'], " ");
        $released = strstr($film['Released'], ' ', true);

        return Film::create([
            'imdb_id' => $imdbId,
            'status' => 'pending',
            'name' => $film['Title'],
            'description' => $film['Plot'],
            'director' => $film['Director'],
            'run_time' => $runTime,
            'released' => $released,
        ]);
    }


    /**
     * Метод добавления фильма в базу, возвращающий информацию о фильме
     *
     * @param User|null $user - null, если пользователь не авторизован, поле isFavorite не выводится
     * @param int|null $idFilm - если нужно вывести информацию по id фильма
     * @return array
     */
    public function showInfoAboutFilm(User $user = null, int|null $idFilm = null): array
    {
        if ($idFilm) {
            $this->film = Film::find($idFilm);
        }

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

        $films = [];

        DB::beginTransaction();
        try {
            foreach ($filmIdsForShow as $filmIdForShow) {
                $films[] = $this->showInfoAboutFilm(idFilm: $filmIdForShow);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::warning($e->getMessage());
        }

        return $films;
    }

}
