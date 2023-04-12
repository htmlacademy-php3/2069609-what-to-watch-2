<?php

namespace App\Http\Responses\Success;

use App\Http\Responses\BaseResponse;
use Illuminate\Pagination\AbstractPaginator;

class SuccessPaginatedResponse extends BaseResponse
{
    /**
     * Формирование содежимого ответа с пагинацией.
     *
     * @return array
     */
    protected function makeResponseData(): array
    {
        if ($this->data instanceof AbstractPaginator) {
            $this->data = $this->data->toArray();
        }

        return [
            'data' => $this->prepareData()
        ];
    }
}
