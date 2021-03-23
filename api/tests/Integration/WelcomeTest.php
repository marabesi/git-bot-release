<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;

class WelcomeTest extends TestCase
{
    use AppTest;

    public function test_renders_request_token_link()
    {
        $response = $this->createRequest('GET', '/');
        $this->assertStringContainsString('<a href="/request-token">Request gitlab permission</a>', (string) $response->getBody());
    }
}

