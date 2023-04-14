<?php

namespace App\Handler;

use App\Http\Repositories\OmdbRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AddFilmFromRepository
{
    /**
     * @throws GuzzleException
     */
    public static function getFilmInfo($imdbId): array
    {
        $client = new Client();
        $movieRepository = new OmdbRepository($client);
        return $movieRepository->getMovies($imdbId);
    }
}
