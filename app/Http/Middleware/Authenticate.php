<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): JsonResponse|string|null
    {
        throw new HttpResponseException(
        //ошибка 401
            response()->json(['message' => 'Запрос требует аутентификации.'], Response::HTTP_UNAUTHORIZED)
        );
    }
}
