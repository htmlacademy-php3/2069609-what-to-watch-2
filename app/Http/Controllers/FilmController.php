<?php
namespace App\Http\Controllers;

use App\Handlers\Permission;
use App\Http\Requests\Film\FilmAddRequest;
use App\Http\Requests\Film\FilmUpdateRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\Fail\FailResponse;
use App\Http\Responses\Fail\ForbiddenResponse;
use App\Http\Responses\Fail\NotFoundResponse;
use App\Http\Responses\Success\SuccessPaginatedResponse;
use App\Http\Responses\Success\SuccessResponse;
use App\Models\Film;
use App\Services\FilmService;
use Auth;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class FilmController extends Controller
{
    /**
     * Получение списка всех фильмов, на каждой странице по 8 фильмов
     *
     * @return BaseResponse
     */
    public function index(): BaseResponse
    {
        try {
            $films = Film::select(['id', 'name', 'preview_image']);
            return new SuccessPaginatedResponse($films->paginate(8));
        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }

    /**
     * Добавление фильма в базу
     *
     * @param FilmAddRequest $request
     * @return BaseResponse
     * @throws GuzzleException|Throwable
     */
    public function store(FilmAddRequest $request): BaseResponse
    {
        try {
            $user = Auth::user();

            if (!Permission::isUserModerator($user)) {
                return new ForbiddenResponse();
            }

            $imdbId = $request->imdb_id;

            $filmService = new FilmService();

            $data = $filmService->addFilm($imdbId);

            return new SuccessResponse($data);
        } catch (Exception $e) {
            return new FailResponse(exception: $e);
        }
    }

    /**
     * Получение инфо о фильме
     *
     * @param int $filmId - id запрашиваемого фильма
     * @param Request $request
     * @return BaseResponse
     */
    public function show(int $filmId, Request $request): BaseResponse
    {
        try {
            $film = Film::find($filmId);

            if (!$film) {
                return new NotFoundResponse();
            }

            $user = $request->user('sanctum');
            $filmService = new FilmService($film);

            $data = $filmService->showInfoAboutFilm($user);

            return new SuccessResponse($data);
        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }

    /**
     * Редактирование фильма
     *
     * @param FilmUpdateRequest $request
     * @param int $filmId - id редактируемого фильма
     * @return BaseResponse
     * @throws Throwable
     */
    public function update(FilmUpdateRequest $request, int $filmId): BaseResponse
    {
        try {
            $film = Film::find($filmId);

            if (!$film) {
                return new NotFoundResponse();
            }

            $user = Auth::user();

            if (!Permission::isUserModerator($user)) {
                return new ForbiddenResponse();
            }

            $filmService = new FilmService($film);

            DB::beginTransaction();
            try {
                $film->update($request->validated());
                $filmService->updateGenresForFilm($request);
                $filmService->updateActorsForFilm($request);
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                Log::warning($e->getMessage());
            }

            $data = $filmService->showInfoAboutFilm($user);

            return new SuccessResponse($data);
        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }

    /**
     * Получение списка из четырех похожих фильмов. Похожесть определяется по жанру.
     *
     * @param int $filmId - id фильма, по которому выдается список похожих фильмов
     * @return BaseResponse
     * @throws Throwable
     */
    public function similar(int $filmId): BaseResponse
    {
        try {
            $film = Film::find($filmId);

            if (!$film) {
                return new NotFoundResponse();
            }

            $filmService = new FilmService($film);
            $data = $filmService->getSimilarFilms();

            return new SuccessResponse($data);

        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }
}
