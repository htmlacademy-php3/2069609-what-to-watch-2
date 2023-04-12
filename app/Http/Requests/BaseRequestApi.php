<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base Form Request for Api.
 * It has not redirected at failed validation
 */
abstract class BaseRequestApi extends FormRequest
{
    /**
     * @param Validator $validator
     * @return Response
     */
    protected function failedValidation(Validator $validator): Response
    {
        $errors = (new ValidationException($validator))->errors();

        throw new HttpResponseException(
            //ошибка 422
            response()->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
