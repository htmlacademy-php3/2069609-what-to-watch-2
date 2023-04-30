<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\Fail\FailResponse;
use App\Http\Responses\Success\SuccessResponse;
use Exception;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Получение списка фильмов из избранного
     */
    public function index(): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (Exception $e) {
            return new FailResponse(exception: $e);
        }
    }

    /**
     * Добавление фильма в избранное
     */
    public function store(Request $request): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (Exception $e) {
            return new FailResponse(exception: $e);
        }
    }

    /**
     * Удаление фильма из избранного
     */
    public function destroy($id): BaseResponse
    {
        if (!$id) {
            return new FailResponse();
        }
        return new SuccessResponse();
    }
}
