<?php

namespace App\Http\Responses\Fail;

use Symfony\Component\HttpFoundation\Response;

class UnauthorizedResponse extends FailResponse
{
    //ошибка 401 - неавторизован
    public function __construct(
        public int                $statusCode = Response::HTTP_UNAUTHORIZED,
        public ?string            $message = 'Запрос требует аутентификации.',
    ) {
        parent::__construct(
            statusCode: $statusCode,
            message: $message
        );
    }

    /**
     * Формирование содежимого ответа.
     *
     * @return array
     */
    protected function makeResponseData(): array
    {
        return [
            'data' => [
                'message' => $this->message,
            ]
        ];
    }
}
