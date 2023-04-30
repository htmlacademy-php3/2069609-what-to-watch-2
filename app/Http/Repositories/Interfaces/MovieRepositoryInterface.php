<?php

namespace App\Http\Repositories\Interfaces;

interface MovieRepositoryInterface
{

    /**
     * Метод получения инфо о фильме по его imdbId

     * @param string $imdbId - id фильма
     * @return array
     */
    public function getMovies(string $imdbId): array;
}
