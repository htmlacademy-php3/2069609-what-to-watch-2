<?php

use Delta\WhatToWatch\Repositories\Interfaces\MovieRepositoryInterface;
use Delta\WhatToWatch\Repositories\OmdbRepository;
use GuzzleHttp\Client;

require_once('vendor/autoload.php');

function getMovie(MovieRepositoryInterface $movieRepository, string $imdbId): array
{
    return $movieRepository->getMovies($imdbId);
}

$imdbId = 'tt3896198';
$client = new Client();
$movieRepository = new OmdbRepository($client);
$movie = getMovie($movieRepository, $imdbId);
var_dump($movie);
