<?php

namespace App\Http\Controllers;

use App\Http\Requests\Genre\GenreUpdateRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\Fail\FailResponse;
use App\Http\Responses\Fail\ForbiddenResponse;
use App\Http\Responses\Fail\NotFoundResponse;
use App\Http\Responses\Success\SuccessResponse;
use App\Models\Genre;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class GenreController extends Controller
{
    /**
     * Получение списка жанров
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
     *
     * @param GenreUpdateRequest $request
     * @param int $genreId
     * @return BaseResponse
     */
    public function update(GenreUpdateRequest $request, int $genreId): BaseResponse
    {
        try {
            $genre = Genre::find($genreId);

            if (!$genre) {
                return new NotFoundResponse();
            }

            if (Gate::denies('edit-resource')) {
                return new ForbiddenResponse();
            }

            $genre->update($request->validated());
            return new SuccessResponse($genre->fresh());
        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }
}
