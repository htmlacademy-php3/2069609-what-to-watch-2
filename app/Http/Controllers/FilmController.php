<?php
namespace App\Http\Controllers;

use App\Http\Requests\Film\FilmAddRequest;
use App\Http\Requests\Film\FilmUpdateRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\Fail\FailResponse;
use App\Http\Responses\Fail\ForbiddenResponse;
use App\Http\Responses\Fail\NotFoundResponse;
use App\Http\Responses\Success\SuccessPaginatedResponse;
use App\Http\Responses\Success\SuccessResponse;
use App\Jobs\AddFilmJob;
use App\Models\ActorFilm;
use App\Models\Film;
use App\Models\FilmGenre;
use App\Services\FilmService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Throwable;

class FilmController extends Controller
{
    private FilmService $filmService;

    public function __construct(FilmService $filmService)
    {
        $this->filmService = $filmService;
    }

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
     * @throws Throwable
     */
    public function store(FilmAddRequest $request): BaseResponse
    {
        try {
            if (Gate::denies('edit-resource')) {
                return new ForbiddenResponse();
            }

            AddFilmJob::dispatch($request->imdb_id);

            return new SuccessResponse(['message' => 'Фильм успешно сохранен в базу'], 201);
        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
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

            $this->filmService->setFilm($film);
            $data = $this->filmService->showInfoAboutFilm($user);

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
        /*
        try {
            $film = Film::find($filmId);

            if (!$film) {
                return new NotFoundResponse();
            }

            if (Gate::denies('edit-resource')) {
                return new ForbiddenResponse();
            }

            $this->filmService->setFilm($film);

            DB::beginTransaction();
            try {
                $film->update($request->validated());
                $this->filmService->updateGenresForFilm($request);
                $this->filmService->updateActorsForFilm($request);
                DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                Log::warning($e->getMessage());
            }

            $data = $this->filmService->showInfoAboutFilm();

            return new SuccessResponse($data);
        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }*/

        $film = Film::find($filmId);

        if (!$film) {
            return new NotFoundResponse();
        }

        if (Gate::denies('edit-resource')) {
            return new ForbiddenResponse();
        }
        DB::beginTransaction();
        try {
            $film->update($request->validated());
            $this->filmService->setFilm($film);

            if ($request->genre_id) {
                $this->filmService->updateGenresForFilm($request->genre_id);
            }
            if ($request->starring_id) {
                $this->filmService->updateActorsForFilm($request->starring_id);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::warning($e->getMessage());
        }

        $data = $this->filmService->showInfoAboutFilm();

        return new SuccessResponse($data);


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

            $this->filmService->setFilm($film);
            $data = $this->filmService->getSimilarFilms();

            return new SuccessResponse($data);

        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }
}
