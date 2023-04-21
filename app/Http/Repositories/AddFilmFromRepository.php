<?php

namespace App\Http\Repositories;

use GuzzleHttp\Exception\GuzzleException;

class AddFilmFromRepository
{
    /**
     * Метод для получения информации о фильме на ресурсе http://www.omdbapi.com
     *
     * @param OmdbRepository $repository - репозиторий работающий с ресурсом http://www.omdbapi.com
     * @param string $imdbId - id запрашиваемого фильма
     * @return array - массив с инф-ей о фильме
     * @throws GuzzleException
     */

    private OmdbRepository $repository;
    private string $imdbId;
    public function __construct(OmdbRepository $repository, string $imdbId)
    {
        $this->repository = $repository;
        $this->imdbId = $imdbId;
    }

    /**
     * @throws GuzzleException
     */
    public function getFilmInfo(): array
    {
        return $this->repository->getMovies($this->imdbId);
    }
}
