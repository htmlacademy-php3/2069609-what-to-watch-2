<?php

namespace App\Http\Controllers;

use App\Http\Responses\BaseResponse;
use App\Http\Responses\FailResponse;
use App\Http\Responses\SuccessResponse;
use Exception;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Регистрация пользователя
     */
    public function register(Request $request): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (Exception $e) {
            return new FailResponse(exception: $e);
        }
    }

    /**
     * Аутентификация пользователя
     */
    public function login(Request $request): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (Exception $e) {
            return new FailResponse(exception: $e);
        }
    }

    /**
     * Выход пользователя из системы
     */
    public function logout(): BaseResponse
    {
        try {
            return new SuccessResponse();
        } catch (Exception $e) {
            return new FailResponse(exception: $e);
        }
    }
}
