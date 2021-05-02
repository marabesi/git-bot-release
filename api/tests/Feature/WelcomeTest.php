<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Gitlab\Authentication\GenerateToken;
use App\Domain\Gitlab\Authentication\TokenRepository;
use App\Domain\Gitlab\Project\SettingsRepository;
use App\Domain\Gitlab\Version\VersionRepository;
use Tests\Feature\Stubs\EmptySettings;
use Tests\Feature\Stubs\FakeVersionError;
use Tests\Feature\Stubs\WithFakeToken;
use Tests\Feature\Stubs\WithoutToken;

class WelcomeTest extends AppTest
{

    public function test_renders_request_token_link()
    {
        $response = $this->createRequest('GET', '/');
        $this->assertStringContainsString('<a href="/request-token">Request gitlab permission</a>', (string) $response->getBody());
    }

    public function test_renders_settings_link()
    {
        $response = $this->createRequest('GET', '/');
        $this->assertStringContainsString('<a href="/settings">settings</a>', (string) $response->getBody());
    }

    public function test_request_gitlab_token()
    {
        $this->container->set(SettingsRepository::class, new EmptySettings());

        $response = $this->createRequest('GET', '/request-token');
        $redirectToGitlab = $response->getHeaderLine('Location');

        $this->assertEquals('/oauth/authorize?client_id=&redirect_uri=&response_type=code&state=&scope=read_repository+write_repository+api', $redirectToGitlab);
    }

    public function test_redirect_to_authorized_page_once_token_has_been_received()
    {
        $generateToken = $this->createMock(GenerateToken::class);
        $generateToken->expects($this->once())
            ->method('requestToken')
            ->willReturn('MY_TOKEN');

        $this->container->set(GenerateToken::class, $generateToken);

        $response = $this->createRequest('GET', '/', [], [
            'code' => 9999,
            'state' => 'SOME_RANDOM_STRING',
        ]);

        $this->assertEquals('/authorized', $response->getHeaderLine('Location'));
    }

    public function test_redirect_to_authorized_page_if_token_exists()
    {
        $this->container->set(VersionRepository::class, new FakeVersionError());
        $this->container->set(TokenRepository::class, new WithFakeToken());

        $response = $this->createRequest('GET', '/');

        $this->assertEquals('/authorized', $response->getHeaderLine('Location'));
    }

    public function test_redirect_to_unauthorized_if_error_when_requesting_token()
    {
        $this->container->set(TokenRepository::class, new WithoutToken());

        $response = $this->createRequest('GET', '/', [], [
            'error' => 'Error authenticating'
        ]);

        $this->assertEquals(
            '/unauthorized',
            $response->getHeaderLine('Location')
        );
    }
}
