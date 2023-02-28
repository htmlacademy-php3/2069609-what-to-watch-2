<?php

use Delta\WhatToWatch\Repositories\OmdbRepository;
use GuzzleHttp\Client;

require_once('vendor/autoload.php');

$client = new Client();
$repository = new OmdbRepository($client);

$imdbId = 'tt3896198';
$movies = $repository->getMovies($imdbId);

var_dump($movies);