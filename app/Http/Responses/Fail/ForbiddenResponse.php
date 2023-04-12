<?php

namespace App\Http\Responses\Fail;

use Symfony\Component\HttpFoundation\Response;

class ForbiddenResponse extends FailResponse
{
    //ошибка 403 - запрещено действие
    public function __construct(
        public int                $statusCode = Response::HTTP_FORBIDDEN,
        public ?string            $message = 'Запрос требует дополнительных прав.',
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
