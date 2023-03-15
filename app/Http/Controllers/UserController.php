<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Получение профиля пользователя
     */
    public function show(): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (Exception $e) {
            return new FailResponse($e);
        }
    }

    /**
     * Обновление профиля пользователя
     */
    public function update(Request $request): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (Exception $e) {
            return new FailResponse($e);
        }
    }
}
