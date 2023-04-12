<?php

namespace App\Http\Requests\Film;

use App\Http\Requests\BaseRequestApi;

class FilmUpdateRequest extends BaseRequestApi
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
            'name' => 'required|string|max:255',
            'poster_image' => 'string|max:255',
            'preview_image' => 'string|max:255',
            'background_image' => 'string|max:255',
            'background_color' => 'string|max:9',
            'video_link' => 'string|max:255',
            'preview_video_link' => 'string|max:255',
            'description' => 'string|max:1000',
            'director'=> 'string|max:255',
            'starring_id' => 'array',
            'genre_id' => 'array',
            'run_time' => 'integer|max:500',
            'released' => 'integer|max:2023',
            'imdb_id' => 'required|string|max:9|',
            'status' => 'required|string'
        ];
    }
}
