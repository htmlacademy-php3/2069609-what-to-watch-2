<?php

namespace App\Http\Responses\Fail;

use Symfony\Component\HttpFoundation\Response;

class NotFoundResponse extends FailResponse
{
    //ошибка 404 - не найдено
    public function __construct(
        public int                $statusCode = Response::HTTP_NOT_FOUND,
        public ?string            $message = 'Запрашиваемая страница не существует.',
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
