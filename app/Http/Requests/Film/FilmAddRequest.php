<?php

namespace App\Http\Requests\Film;

use App\Http\Requests\BaseRequestApi;

class FilmAddRequest extends BaseRequestApi
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'imdb_id' => 'required|unique:films|regex:/(^tt\d{7}$)/|min:9|max:9',
        ];
    }
}
