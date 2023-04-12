<?php

namespace App\Http\Controllers;

use App\Http\Requests\Genre\GenreUpdateRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\Fail\FailResponse;
use App\Http\Responses\Fail\ForbiddenResponse;
use App\Http\Responses\Fail\NotFoundResponse;
use App\Http\Responses\Success\SuccessResponse;
use App\Models\Genre;
use Auth;
use Exception;
use Illuminate\Support\Facades\DB;

class GenreController extends Controller
{
    /**
     * Получение списка жанров
     * Доступно всем
     *
     * @return BaseResponse
     */
    public function index(): BaseResponse
    {
        try {
            $genres = DB::table('genres')->pluck('title');
            return new SuccessResponse($genres);
        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }

    /**
     * Редакторивание жанра
     * Доступно только авторизованному модератору
     *
     * @param GenreUpdateRequest $request
     * @param int $genreId
     * @return BaseResponse
     */
    public function update(GenreUpdateRequest $request, int $genreId): BaseResponse
    {
        try {
            if (!$genreId) {
                return new NotFoundResponse();
            }

            if ((Auth::user()->is_moderator == 0)) {
                return new ForbiddenResponse();
            }

            $genreIds = DB::table('genres')->pluck('id')->all();

            if (!in_array($genreId, $genreIds)) {
                return new NotFoundResponse();
            }

            $genre = Genre::find($genreId);
            //Смотрит в запросе, что надо изменить (название жанра), валидирует, обновляет если все ок
            $genre->update($request->validated());
            return new SuccessResponse($genre->fresh());
        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }
}
