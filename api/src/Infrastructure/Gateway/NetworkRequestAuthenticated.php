<?php
declare(strict_types=1);

namespace App\Infrastructure\Gateway;

use App\Domain\Gitlab\Authentication\TokenRevoked;
use App\Domain\Gitlab\Entity\Settings;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class NetworkRequestAuthenticated
{
    private Settings $settings;
    private Client $client;
    private array $headers;

    public function __construct(string $token, Settings $settings, Client $client)
    {
        $this->settings = $settings;
        $this->client = $client;
        $this->headers = [
            'authorization' => sprintf('Bearer %s', $token),
            'cache-control' => 'no-cache',
            'content-type' => 'application/json',
        ];
    }

    public function put(string $url, array $fieldsToPut): array
    {
        $response = $this->client->request(
            'PUT',
            $this->settings->resolveGitlabUri($url),
            [
                'headers' => $this->headers,
                'json' => $fieldsToPut,
            ],
        );

        return (array) json_decode((string) $response->getBody(), true);
    }

    public function post(string $url, array $fieldsToPost): array
    {
        $response = $this->client->request(
            'POST',
            $this->settings->resolveGitlabUri($url),
            [
                'headers' => $this->headers,
                'json' => $fieldsToPost,
            ],
        );

        return (array) json_decode((string) $response->getBody(), true);
    }

    /**
     * @throws GuzzleException
     * @throws TokenRevoked
     */
    public function get(string $url, array $params = []): array
    {
        try {

            $query = http_build_query($params);

            $gitlab = $this->settings->resolveGitlabUri($url);
            $url = sprintf("$gitlab?%s", $query);

            $response = $this->client->request(
                'GET',
                $url,
                [
                    'headers' => $this->headers
                ],
            );


            return (array) json_decode((string) $response->getBody(), true);
        } catch (RequestException $error){
            if ($error->getCode() === 401) {
                throw new TokenRevoked();
            }

            throw $error;
        }
    }

    public function delete($url): array
    {
        $response = $this->client->request(
            'DELETE',
            $this->settings->resolveGitlabUri($url),
            [
                'headers' => $this->headers
            ],
        );

        return (array) json_decode((string) $response->getBody(), true);
    }
}