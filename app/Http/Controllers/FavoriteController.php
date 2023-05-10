<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\Fail\FailResponse;
use App\Http\Responses\Fail\NotFoundResponse;
use App\Http\Responses\Fail\UnauthorizedResponse;
use App\Http\Responses\Success\SuccessResponse;
use App\Models\Favorite;
use App\Models\Film;
use Exception;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Получение списка фильмов из избранного
     *
     * @return BaseResponse
     */
    public function index(): BaseResponse
    {
        try {
            if (!Auth::check()) {
                return new UnauthorizedResponse();
            }

            $user = Auth::user();
            $favorites = $user->favorites();
            $favoritesInfo = $favorites->select('film_id as id', 'name', 'preview_image')->get();

            return new SuccessResponse($favoritesInfo);
        } catch (Exception $e) {
            return new FailResponse(exception: $e);
        }
    }

    /**
     * Добавление фильма в избранное
     *
     * @param  int $filmId - id добавляемого в избранное фильма
     * @return BaseResponse
     */
    public function store(int $filmId): BaseResponse
    {
        try {
            if (!Auth::check()) {
                return new UnauthorizedResponse();
            }

            $film = Film::find($filmId);

            if (!$film) {
                return new NotFoundResponse();
            }

            $user = Auth::user();

            if ($user->favorites()->where(['film_id' => $filmId])->first()) {
                return new FailResponse(statusCode: 422, message: 'Данный фильм уже у вас в избранном');
            }

            Favorite::create(['film_id' => $filmId, 'user_id' => $user->id]);

            return new SuccessResponse(['message' => 'Фильм успешно добавлен в избранное']);
        } catch (Exception $e) {
            return new FailResponse(exception: $e);
        }
    }

    /**
     * Удаление фильма из избранного
     *
     * @param  int $filmId - id удаляемого из избранного фильма
     * @return BaseResponse
     */
    public function destroy(int $filmId): BaseResponse
    {
        try {
            if (!Auth::check()) {
                return new UnauthorizedResponse();
            }

            $film = Film::find($filmId);

            if (!$film) {
                return new NotFoundResponse();
            }

            $user = Auth::user();
            $favorite = $user->favorites()->where(['film_id' => $filmId])->first();

            if (!$favorite) {
                return new FailResponse(statusCode: 422, message: 'Данного фильма у вас нет в избранном');
            }

            $favorite->delete();

            return new SuccessResponse(['message' => 'Фильм успешно удален из избранного']);
        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }
}
