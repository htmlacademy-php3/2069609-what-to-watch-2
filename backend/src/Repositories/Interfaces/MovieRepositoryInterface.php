<?php

namespace Delta\WhatToWatch\Repositories\Interfaces;

interface MovieRepositoryInterface
{
    public function getMovies(string $imdbId): array;
}