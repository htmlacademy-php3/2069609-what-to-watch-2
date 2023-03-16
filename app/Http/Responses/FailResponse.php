<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FailResponse extends BaseResponse
{
    public function __construct(
        protected array|Throwable $data = [],
        public int                $statusCode = Response::HTTP_BAD_REQUEST,
        public ?string            $message = null,
        public ?Throwable         $exception = null
    ) {
        if ($exception) {
            $this->message = $exception->getMessage();
            $this->statusCode = $exception->getCode();
        }
        parent::__construct($data, $statusCode, $message);
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
