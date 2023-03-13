<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Exception;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     * Получение списка жанров
     */
    public function index(): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (Exception $e) {
            return new FailResponse([], $e->getMessage());
        }
    }

    /**
     * Редакторивание жанра
     */
    public function update(Request $request, $id): BaseResponse
    {
        if (!$id) {
            return new FailResponse([]);
        }
        return new SuccessResponse();
    }
}
