<?php

namespace Tests\Feature;

use App\Domain\Gitlab\Entity\Settings;
use PHPUnit\Framework\TestCase;

class WelcomeTest extends TestCase
{
    use AppTest;

    public function test_renders_request_token_link()
    {
        $response = $this->createRequest('GET', '/');
        $this->assertStringContainsString('<a href="/request-token">Request gitlab permission</a>', (string) $response->getBody());
    }

    public function test_request_gitlab_token()
    {
        $this->container->set(Settings::class, new Settings(
            '',
            '',
            '',
            '',
            '',
        ));

        $response = $this->createRequest('GET', '/request-token');
        $redirectToGitlab = $response->getHeaderLine('Location');

        $this->assertEquals('/oauth/authorize?client_id=&redirect_uri=&response_type=code&state=&scope=read_repository+write_repository+api', $redirectToGitlab);
    }
}
