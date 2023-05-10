<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\Fail\FailResponse;
use App\Http\Responses\Fail\ForbiddenResponse;
use App\Http\Responses\Fail\NotFoundResponse;
use App\Http\Responses\Fail\UnauthorizedResponse;
use App\Http\Responses\Success\SuccessResponse;
use App\Models\Film;
use App\Services\FilmService;
use Auth;
use Exception;
use Illuminate\Support\Facades\Gate;
use Throwable;

class PromoController extends Controller
{
    private FilmService $filmService;

    public function __construct(FilmService $filmService)
    {
        $this->filmService = $filmService;
    }

    /**
     * Получение промо-фильма
     * Доступно всем
     *
     * @return BaseResponse
     */
    public function index(): BaseResponse
    {
        try {
            $film = Film::where(['is_promo' => 1])->first();
            $this->filmService->setFilm($film);

            $filmInfo = $this->filmService->showInfoAboutFilm();
            return new SuccessResponse($filmInfo);
        } catch (Exception $e) {
            return new FailResponse(exception: $e);
        }
    }

    /**
     * Установка промо-фильма
     * Доступно авторизованному модератору
     *
     * @param int $filmId - id фильма, который должен стать промо-фильмом
     * @return BaseResponse
     * @throws Throwable
     */
    public function store(int $filmId): BaseResponse
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return new UnauthorizedResponse();
            }

            if (Gate::denies('edit-resource')) {
                return new ForbiddenResponse();
            }

            $promoFilm = Film::find($filmId);

            if (!$promoFilm) {
                return new NotFoundResponse();
            }

            $this->filmService->setFilm($promoFilm);
            $data = $this->filmService->getNewPromoFilm();

            return new SuccessResponse($data);
        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }
}
