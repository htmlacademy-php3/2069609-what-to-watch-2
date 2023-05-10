<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Responses\BaseResponse;
use App\Http\Responses\Fail\FailResponse;
use App\Http\Responses\Fail\UnauthorizedResponse;
use App\Http\Responses\Success\SuccessResponse;
use App\Services\UserService;
use Auth;
use Exception;

class UserController extends Controller
{

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Получение профиля пользователя
     *
     * @return BaseResponse
     */
    public function show(): BaseResponse
    {
        try {
            if (!Auth::check()) {
                return new UnauthorizedResponse();
            }

            $user = Auth::user();
            $this->userService->setUser($user);
            $userInfo = $this->userService->getInfo();

            return new SuccessResponse($userInfo);
        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }

    /**
     * Обновление профиля пользователя
     *
     * @param UserUpdateRequest $request
     * @return BaseResponse
     */
    public function update(UserUpdateRequest $request): BaseResponse
    {
        try {
            if (!Auth::check()) {
                return new UnauthorizedResponse();
            }
            $user = Auth::user();
            $user->update($request->validated());

            $this->userService->setUser($user);
            $userInfo = $this->userService->getInfo();

            return new SuccessResponse($userInfo);

        } catch (Exception $e) {
            return new FailResponse(statusCode: 500, exception: $e);
        }
    }
}
