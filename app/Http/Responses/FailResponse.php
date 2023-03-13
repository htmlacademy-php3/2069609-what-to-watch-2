<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

class FailResponse extends BaseResponse
{
    public int $statusCode = Response::HTTP_BAD_REQUEST;

    /**
     * ExceptionResponse constructor.
     *
     * @param $data
     * @param string|null $message
     * @param int $code
     */
    public function __construct($data, protected ?string $message = null, int $code = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct([], $code);
    }

    /**
     * Формирование содежимого ответа.
     *
     * @return array
     */
    protected function makeResponseData(): array
    {
        return [
            'message' => $this->message,
            'errors' => $this->prepareData(),
        ];
    }
}
