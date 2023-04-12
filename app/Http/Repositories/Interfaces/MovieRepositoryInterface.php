<?php

namespace App\Http\Repositories\Interfaces;

interface MovieRepositoryInterface
{
    public function getMovies(string $imdbId): array;
}
