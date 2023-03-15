<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Получение списка комментариев к фильму
     */
    public function index(Request $request, $id): BaseResponse
    {
        if (!$id) {
            return new FailResponse([]);
        }
        return new SuccessResponse();
    }

    /**
     * Добавление комментария к фильму
     */
    public function store(Request $request, $id): BaseResponse
    {
        if (!$id) {
            return new FailResponse([]);
        }
        return new SuccessResponse();
    }

    /**
     * Редактирование комментария
     */
    public function update(Request $request, $id): BaseResponse
    {
        if (!$id) {
            return new FailResponse();
        }
        return new SuccessResponse();
    }

    /**
     * Удаление комментария
     */
    public function destroy($id): BaseResponse
    {
        if (!$id) {
            return new FailResponse();
        }
        return new SuccessResponse();
    }
}
