<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Exception;
use Illuminate\Http\Request;

class FilmController extends Controller
{
    /**
     * Получение списка фильмов
     */
    public function index(): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (Exception $e) {
            return new FailResponse($e);
        }
    }

    /**
     * Добавление фильма в базу
     */
    public function store(Request $request): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (Exception $e) {
            return new FailResponse($e);
        }
    }

    /**
     * Получение инфо о фильме
     */
    public function show($id): BaseResponse
    {
        if (!$id) {
            return new FailResponse();
        }
        return new SuccessResponse();
    }

    /**
     * Редактирование фильма
     */
    public function update(Request $request, $id): BaseResponse
    {
        if (!$id) {
            return new FailResponse();
        }
        return new SuccessResponse();
    }

    /**
     * Получение списка похожих фильмов
     */
    public function getSimilar($id): BaseResponse
    {
        if (!$id) {
            return new FailResponse();
        }
        return new SuccessResponse();
    }
}
