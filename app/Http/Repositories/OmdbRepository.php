<?php

namespace App\Http\Repositories;

use App\Http\Repositories\Interfaces\MovieRepositoryInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class OmdbRepository implements MovieRepositoryInterface
{
    private string $apiKey = 'b76a7be1';
    private string $baseUri = 'https://www.omdbapi.com/';
    private ClientInterface $client;

    public function __construct(ClientInterface $httpClient)
    {
        $this->client = $httpClient;
    }

    /**
     * @throws GuzzleException
     */
    public function sendRequest(string $imdbId): ResponseInterface
    {
        return
            $response = $this->client->request('GET', $this->baseUri, [
                'query' =>
                    [
                        'apikey' => $this->apiKey,
                        'i' => $imdbId
                    ]
            ]);
    }


    /**
     * @throws GuzzleException
     */
    public function getMovies(string $imdbId): array
    {
        $response = $this->sendRequest($imdbId);
        return json_decode($response->getBody()->getContents(), true);
    }

}
