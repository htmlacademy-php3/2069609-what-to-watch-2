<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\Fail\FailResponse;
use App\Http\Responses\Success\SuccessResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{
    /**
     * Регистрация пользователя
     *
     * @param RegisterRequest $request
     * @return BaseResponse
     */

    public function register(RegisterRequest $request): BaseResponse
    {
        try {
            $params = $request->safe()->except('avatar');
            $params['password'] = Hash::make($params['password']);

            $user = User::create($params);

            $token = $user->createToken('auth-token');

            $data = [
                'user' => $user,
                'token' => $token->plainTextToken,
            ];
            return new SuccessResponse($data, 201);
        } catch (Throwable $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }

    /**
     * Аутентификация пользователя
     *
     * @param LoginRequest $request
     * @return BaseResponse
     */
    public function login(LoginRequest $request): BaseResponse
    {
        try {
            if (!Auth::attempt($request->validated())) {
                abort(401, trans('auth.failed'));
            }

            $user = $request->user();
            $token = $user->createToken('auth-token');
            $data = [
                'user' => $user,
                'token' => $token->plainTextToken,
            ];
            return new SuccessResponse(data: $data);
        }
        catch (Throwable $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }

    }

    /**
     * Выход пользователя из системы
     *
     * @return BaseResponse
     */
    public function logout(): BaseResponse
    {
        try {
            Auth::user()->tokens()->delete();
            $data = [
                'message' => 'Успешный выход из системы'
            ];
            return new SuccessResponse(data: $data);
        } catch (Throwable $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }
}
