<?php
declare(strict_types=1);

namespace Tests\Feature\Settings;

use App\Domain\Gitlab\Entity\Settings;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use App\Infrastructure\Persistence\Gitlab\SettingsFilesystemRepository;
use App\UseCases\Gitlab\Settings\GetGitlabSettings;
use App\UseCases\Gitlab\Settings\SaveGitlabSettings;
use PHPUnit\Framework\TestCase;
use Tests\Feature\AppTest;

class SettingsTest extends TestCase
{

    use AppTest;
    private Settings $settings;
    const SETTINGS_URI = '/settings';

    public function __construct()
    {
        parent::__construct();
        $this->settings = new Settings(
            'http://mygitlab.com',
            '123456',
            '999',
            'http://git-bot-release.com',
            'my_secret'
        );
    }

    public function test_save_settings()
    {
        $saveUseCase = $this->createMock(SaveGitlabSettings::class);
        $saveUseCase->expects($this->once())
            ->method('save');

        $this->container->set(SaveGitlabSettings::class, $saveUseCase);

        $response = $this->createRequest('POST', self::SETTINGS_URI, [
            'gitlab_url' => $this->settings->getRedirectUrl(),
            'client_id' => $this->settings->getClientId(),
            'secret' => $this->settings->getSecret(),
            'redirect_url' => $this->settings->getRedirectUrl(),
            'state' => $this->settings->getState(),
        ]);

        $redirectTo = $response->getHeaderLine('Location');
        $this->assertEquals(self::SETTINGS_URI, $redirectTo);
    }

    public function test_list_settings()
    {
        $saveUseCase = $this->createMock(GetGitlabSettings::class);
        $saveUseCase->expects($this->once())
            ->method('list')
            ->willReturn($this->settings);

        $this->container->set(GetGitlabSettings::class, $saveUseCase);

        $response = $this->createRequest('GET', self::SETTINGS_URI);
        $body = (string) $response->getBody();

        $this->assertStringContainsString($this->settings->getGitlabUrl(), $body, 'gitlab url does not match');
        $this->assertStringContainsString($this->settings->getClientId(), $body, 'client id does not match');
        $this->assertStringContainsString($this->settings->getSecret(), $body, 'secret does not match');
        $this->assertStringContainsString($this->settings->getRedirectUrl(), $body, 'redirect url does not match');
        $this->assertStringContainsString($this->settings->getState(), $body, 'state does not match');
    }
}

class NetworkStub extends NetworkRequestAuthenticated
{
    public function get(string $url, array $params = []): array
    {
        return [];
    }
}

class SettingsFilesystemStub extends SettingsFilesystemRepository
{
    public function store(Settings $settings): bool
    {
        return true;
    }
}