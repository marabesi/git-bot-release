<?php
declare(strict_types=1);

namespace App\Infrastructure\Gateway;

use GuzzleHttp\Client;

class NetworkRequest
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function post(string $url, array $fieldsToPost): array
    {
        $response = $this->client->request('POST', $url, ['form_params' => $fieldsToPost ]);

        $body = (string) $response->getBody()->getContents();

        return (array) json_decode($body, true, 512, JSON_THROW_ON_ERROR);
    }
}