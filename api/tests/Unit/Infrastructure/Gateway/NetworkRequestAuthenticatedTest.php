<?php
declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway;

use App\Domain\Gitlab\Authentication\TokenRevoked;
use App\Domain\Gitlab\Entity\Settings;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

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
    private string $endpoint = 'endpoint';

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
        $endpoint = sprintf('%s/%s', $this->settings->getGitlabUrl(), $this->endpoint);
        $toPost = [
            'headers' => $this->headers,
            'json' => $this->requestParameters,
        ];

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('POST', $endpoint, $toPost);

        $network = new NetworkRequestAuthenticated(
            self::TOKEN,
            $this->settings,
            $client
        );

        $network->post('endpoint', $this->requestParameters);
    }

    public function test_put_request_with_authentication_headers()
    {
        $endpoint = sprintf('%s/%s', $this->settings->getGitlabUrl(), $this->endpoint);
        $toPut = [
            'headers' => $this->headers,
            'json' => $this->requestParameters,
        ];

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('PUT', $endpoint, $toPut);

        $network = new NetworkRequestAuthenticated(
            self::TOKEN,
            $this->settings,
            $client
        );

        $network->put('endpoint', $this->requestParameters);
    }

    public function test_get_request_with_authentication_headers()
    {
        $endpoint = sprintf('%s/endpoint?param1=param1', $this->settings->getGitlabUrl());

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('GET', $endpoint, [ 'headers' => $this->headers ]);

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
            ->with('DELETE', $endpoint, [ 'headers' => $this->headers ]);

        $network = new NetworkRequestAuthenticated(
            self::TOKEN,
            $this->settings,
            $client
        );

        $network->delete('endpoint');
    }

    public function test_error_revoked_token_if_get_request_is_unauthorized()
    {
        $this->expectException(TokenRevoked::class);

        $request = (new ServerRequestFactory())->createServerRequest('GET', $this->endpoint);
        $response = (new ResponseFactory())->createResponse(401);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->willThrowException(new RequestException('unauthorized', $request, $response));

        $network = new NetworkRequestAuthenticated(
            self::TOKEN,
            $this->settings,
            $client
        );

        $network->get($this->endpoint);
    }

    public function test_bubble_up_error_if_status_code_is_not_unauthorized()
    {
        $this->expectException(RequestException::class);

        $request = (new ServerRequestFactory())->createServerRequest('GET', $this->endpoint);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->willThrowException(new RequestException('unauthorized', $request));

        $network = new NetworkRequestAuthenticated(
            self::TOKEN,
            $this->settings,
            $client
        );

        $network->get($this->endpoint);
    }
}