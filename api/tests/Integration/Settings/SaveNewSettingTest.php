<?php

namespace Tests\Integration\Settings;

use App\Domain\Gitlab\Authentication\CouldNotEraseTokenException;
use App\Domain\Gitlab\Authentication\CouldNotStoreTokenException;
use App\Domain\Gitlab\Authentication\TokenNotFound;
use App\Domain\Gitlab\Authentication\TokenRepository;
use App\Domain\Gitlab\Entity\Settings;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use PHPUnit\Framework\TestCase;
use Tests\Integration\AppTest;

class SaveNewSettingTest extends TestCase
{

    use AppTest;
    private Settings $settings;

    public function __construct()
    {
        parent::__construct();
        $this->settings = new Settings(
            'http://mygitlab.com',
            '123',
            '123',
            'http://git-bot-release.com',
            '123123'
        );
    }

    public function test_should_ask_for_setup_settings_if_it_not_exists()
    {
        $this->container->set(TokenRepository::class, new NoTokenStub());
        $this->container->set(NetworkRequestAuthenticated::class, new NetworkStub('123123', $this->settings));

        $response = $this->createRequest('GET', '/');
        $this->assertStringContainsString('/request-token', $response->getBody());
    }

    public function test_save_settings()
    {
        $this->container->set(TokenRepository::class, new NoTokenStub());
        $this->container->set(NetworkRequestAuthenticated::class, new NetworkStub('123123', $this->settings));

        $response = $this->createRequest('POST', '/settings', [
            'gitlab_url' => 'asdasd.coa',
            'client_id' => 'asasdasd',
            'secret' => 'asdasd',
            'redirect_url' => 'asdasdasd.cc',
            'state' => '1111',
        ]);

        $this->assertTrue(
            $response->hasHeader('Location'),
            'Save settings has no location header in the response'
        );
    }
}

class TokenStub implements TokenRepository
{

    /**
     * @param $token string
     * @return bool
     * @throws CouldNotStoreTokenException
     */
    public function storeToken(string $token): bool
    {
        return true;
    }

    /**
     * @throws TokenNotFound
     */
    public function getToken(): string
    {
        return 'fake_token';
    }

    /**
     * @throws CouldNotEraseTokenException
     */
    public function deleteToken(): bool
    {
        return true;
    }
}

class NoTokenStub implements TokenRepository
{

    /**
     * @param $token string
     * @return bool
     * @throws CouldNotStoreTokenException
     */
    public function storeToken(string $token): bool
    {
        return false;
    }

    /**
     * @throws TokenNotFound
     */
    public function getToken(): string
    {
        return '';
    }

    /**
     * @throws CouldNotEraseTokenException
     */
    public function deleteToken(): bool
    {
        return false;
    }
}

class NetworkStub extends NetworkRequestAuthenticated
{

    public function get(string $url, array $params = []): array
    {
        return [];
    }
}