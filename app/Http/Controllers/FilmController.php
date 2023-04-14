<?php

namespace App\Http\Controllers;

use App\Handler\AddFilmFromRepository;
use App\Handler\GetFilmInfo;
use App\Http\Requests\Film\FilmAddRequest;
use App\Http\Requests\Film\FilmUpdateRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\Fail\FailResponse;
use App\Http\Responses\Fail\ForbiddenResponse;
use App\Http\Responses\Fail\NotFoundResponse;
use App\Http\Responses\Success\SuccessPaginatedResponse;
use App\Http\Responses\Success\SuccessResponse;
use App\Models\ActorFilm;
use App\Models\Film;
use App\Models\FilmGenre;
use Auth;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    /**
     * Получение списка всех фильмов, на каждой странице по 8 фильмов
     * Доступно всем
     *
     * @return BaseResponse
     */
    public function index(): BaseResponse
    {
        try {
            $films = Film::select(['id', 'name', 'preview_image']);
            return new SuccessPaginatedResponse($films->paginate(8));
        } catch (Exception $e) {
            return new FailResponse(statusCode:500, exception: $e);
        }
    }

    /**
     * Добавление фильма в базу
     * Доступно авторизованному модератору
     *
     * @param FilmAddRequest $request
     * @return BaseResponse
     * @throws GuzzleException
     */
    public function store(FilmAddRequest $request): BaseResponse
    {
        try {
            if (Auth::user()->is_moderator == 0) {
                return new ForbiddenResponse();
            }
            $imdbId = $request->imdb_id;
            $film = AddFilmFromRepository::getFilmInfo($imdbId);

            $runTime = strtok($film['Runtime'], " ");
            $released = strstr($film['Released'], ' ', true);
            Film::create([
                'imdb_id' => $imdbId,
                'status' => 'pending',
                'name' => $film['Title'],
                'description' => $film['Plot'],
                'director' => $film['Director'],
                'run_time' => $runTime,
                'released' => $released,
                ]);

            $addFilmId = Film::where('imdb_id', '=', $imdbId)->pluck('id');
            $addFilm = Film::find($addFilmId);

            return new SuccessResponse($addFilm);
        } catch (Exception $e) {
            return new FailResponse(exception: $e);
        }
    }

    /**
     * Получение инфо о фильме
     * Доступно всем. Авторизованному пользователю доп поле - любимый фильм или нет.
     *
     * @param $filmId - id запрашиваемого фильма
     * @param Request $request
     * @return BaseResponse
     */
    public function show($filmId, Request $request): BaseResponse
    {
        try {
            if (!$filmId) {
                return new NotFoundResponse();
            }

            $film = Film::find($filmId);

            if (!$film) {
                return new NotFoundResponse();
            }

            $film = GetFilmInfo::getInfo($filmId);

            $user = $request->user('sanctum');

            if ($user) {
                if ($user->favorites()->where('film_id', '=', $filmId)->first()) {
                    $film['isFavorite'] = 1;
                } else {
                    $film['isFavorite'] = 0;
                }
            }
            return new SuccessResponse($film);
        } catch (Exception $e) {
            return new FailResponse(statusCode:500, exception: $e);
        }
    }

    /**
     * Редактирование фильма
     * Доступно авторизованному модератору.
     *
     * @param FilmUpdateRequest $request
     * @param $filmId - id редактируемого фильма
     * @return BaseResponse
     */
    public function update(FilmUpdateRequest $request, $filmId): BaseResponse
    {
        try {
            if (!$filmId) {
                return new NotFoundResponse();
            }

            $film = Film::find($filmId);

            if (!$film) {
                return new NotFoundResponse();
            }

            $user = Auth::user();

            if ($user->is_moderator == 0) {
                return new ForbiddenResponse();
            }

            $film->update($request->validated());


            if ($request->genre_id) {
                DB::delete('delete from film_genre where film_id = ?', [$filmId]);
                foreach ($request->genre_id as $genreId) {
                    FilmGenre::create([
                        'film_id' => $filmId,
                        'genre_id' => $genreId
                    ]);
                }
            }

            if ($request->starring_id) {
                DB::delete('delete from actor_film where film_id = ?', [$filmId]);
                foreach ($request->starring_id as $starringId) {
                    ActorFilm::create([
                        'film_id' => $filmId,
                        'actor_id' => $starringId
                    ]);
                }
            }

            $filmUpdate = GetFilmInfo::getInfo($filmId);

            return new SuccessResponse($filmUpdate);

        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }


    /**
     * Получение списка из четырех похожих фильмов. Похожесть определяется по жанру.
     * Доступно всем
     *
     * @param $filmId - id фильма, по которому выдается список похожих фильмов
     * @return BaseResponse
     */
    public function similar($filmId): BaseResponse
    {
        try {
            if (!$filmId) {
                return new NotFoundResponse();
            }

            $film = Film::find($filmId);

            if (!$film) {
                return new NotFoundResponse();
            }

            $genreIds = $film->genres->pluck('id');

            $allSimilarFilmsIds = [];

            foreach ($genreIds as $genreId) {
                $allSimilarFilmsIds = FilmGenre::where('genre_id', '=' , $genreId)->pluck('film_id')->all();
            }

            $uniqueSimilarFilmsIds = array_unique($allSimilarFilmsIds);
            $keyIdForDelete = array_search($filmId, $uniqueSimilarFilmsIds);

            unset($uniqueSimilarFilmsIds[$keyIdForDelete]);
            shuffle($uniqueSimilarFilmsIds);

            $filmIdsForShow = array_slice($uniqueSimilarFilmsIds, 0, 4);

            $films = [];
            foreach ($filmIdsForShow as $filmIdForShow){
                $films[] = GetFilmInfo::getInfo($filmIdForShow);
            }

            return new SuccessResponse($films);

        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }
}
