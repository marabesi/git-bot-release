<?php
declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway;

use App\Infrastructure\Gateway\NetworkRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use JsonException;

class NetworkRequestTest extends TestCase
{

    private $parameters = [
        'form_params' => []
    ];

    public function test_dispatch_post_request_without_parameters()
    {
        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('POST','/random', $this->parameters)
            ->willReturn(new Response(200, [], '{"message": true}'));

        $network = new NetworkRequest($client);
        $response = $network->post('/random', []);

        $this->assertArrayHasKey('message', $response);
    }

    public function test_handle_response_is_not_json()
    {
        $this->expectException(JsonException::class);

        $client = $this->createMock(Client::class);
        $client->expects($this->once())
            ->method('request')
            ->with('POST','/random', $this->parameters)
            ->willReturn(new Response(200, [], 'message'));

        $network = new NetworkRequest($client);
        $response = $network->post('/random', []);

        $this->assertArrayHasKey('message', $response);
    }
}