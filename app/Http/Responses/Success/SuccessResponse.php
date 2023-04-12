<?php

namespace App\Http\Responses\Success;

use App\Http\Responses\BaseResponse;

class SuccessResponse extends BaseResponse
{
    /**
     * Формирование содежимого ответа.
     *
     * @return array
     */
    protected function makeResponseData(): array
    {
        return [
            'data' => $this->prepareData()
        ];
    }
}
