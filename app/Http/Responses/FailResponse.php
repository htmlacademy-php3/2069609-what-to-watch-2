<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class FailResponse extends BaseResponse
{
    public function __construct(
        protected mixed   $data = [],
        public int        $statusCode = Response::HTTP_BAD_REQUEST,
        public ?string    $message = null,
        public ?Throwable $exception = null
    ) {
        if ($exception) {
            $this->message = $exception->getMessage();
            $this->statusCode = $exception->getCode();
        }
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
