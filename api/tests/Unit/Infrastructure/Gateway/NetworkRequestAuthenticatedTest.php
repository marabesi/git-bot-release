<?php
declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway;

use App\Domain\Gitlab\Entity\Settings;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class NetworkRequestAuthenticatedTest extends TestCase
{

    private Settings $settings;
    private const TOKEN = 'my_token';
    private array $requestParameters = [
        'param1' => 'param1'
    ];
    private array $headers = [
        'authorization' => 'Bearer '. self::TOKEN,
        'cache-control' => 'no-cache',
        'content-type' => 'application/json',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->settings = new Settings(
            'https://my.gitlab.com',
            '11111',
            '111111',
            'https://allowed',
            '22222',
        );
    }

    public function test_post_request_with_authentication_headers()
    {
        $endpoint = sprintf('%s/endpoint', $this->settings->getGitlabUrl());

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('POST', $endpoint, $this->headers, $this->requestParameters);

        $network = new NetworkRequestAuthenticated(
            self::TOKEN,
            $this->settings,
            $client
        );

        $network->post('endpoint', $this->requestParameters);
    }

    public function test_put_request_with_authentication_headers()
    {
        $endpoint = sprintf('%s/endpoint', $this->settings->getGitlabUrl());

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('PUT', $endpoint, $this->headers, $this->requestParameters);

        $network = new NetworkRequestAuthenticated(
            self::TOKEN,
            $this->settings,
            $client
        );

        $network->put('endpoint', $this->requestParameters);
    }

    public function test_get_request_with_authentication_headers()
    {
        $endpoint = sprintf('%s/endpoint', $this->settings->getGitlabUrl());

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('GET', $endpoint, $this->headers, $this->requestParameters);

        $network = new NetworkRequestAuthenticated(
            self::TOKEN,
            $this->settings,
            $client
        );

        $network->get('endpoint', $this->requestParameters);
    }

    public function test_delete_request_with_authentication_headers()
    {
        $endpoint = sprintf('%s/endpoint', $this->settings->getGitlabUrl());

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('DELETE', $endpoint, $this->headers);

        $network = new NetworkRequestAuthenticated(
            self::TOKEN,
            $this->settings,
            $client
        );

        $network->delete('endpoint');
    }
}