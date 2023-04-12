<?php

namespace App\Handler;

use App\Http\Repositories\Interfaces\MovieRepositoryInterface;
use App\Http\Repositories\OmdbRepository;
use GuzzleHttp\Client;

class AddFilmFromRepository
{
    private function getMovie(MovieRepositoryInterface $movieRepository, string $imdbId): array
    {
        return $movieRepository->getMovies($imdbId);
    }

    public static function getFilm($imdbId): array
    {
        $client = new Client();
        $movieRepository = new OmdbRepository($client);
        return (new AddFilmFromRepository)->getMovie($movieRepository, $imdbId);
    }

}
