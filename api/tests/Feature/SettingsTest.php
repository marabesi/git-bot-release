<?php
declare(strict_types=1);

namespace Tests\Feature\Settings;

use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Entity\Webhook;
use App\UseCases\Gitlab\Settings\GetGitlabSettings;
use App\UseCases\Gitlab\Settings\SaveGitlabSettings;
use Tests\Feature\AppTest;

class SettingsTest extends AppTest
{

    private Settings $settings;
    private Webhook $webhook;

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

        $this->webhook = new Webhook(
            'http://webhook.com',
            '123',
            true,
            true
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
            'webhook_url' => $this->webhook->getUrl(),
            'webhook_token' => $this->webhook->getToken(),
            'webhook_push_events' => $this->webhook->getPushEvents(),
            'webhook_enable_ssl_verification' => $this->webhook->getEnableSslVerification(),
        ]);

        $redirectTo = $response->getHeaderLine('Location');

        $this->assertEquals('/', $redirectTo);
    }

    public function test_list_gitlab_settings()
    {
        $saveUseCase = $this->createMock(GetGitlabSettings::class);
        $saveUseCase->expects($this->once())
            ->method('list')
            ->willReturn([
                'gitlab' => $this->settings
            ]);

        $this->container->set(GetGitlabSettings::class, $saveUseCase);

        $response = $this->createRequest('GET', self::SETTINGS_URI);
        $body = (string) $response->getBody();

        $this->assertStringContainsString($this->settings->getGitlabUrl(), $body, 'gitlab url does not match');
        $this->assertStringContainsString($this->settings->getClientId(), $body, 'client id does not match');
        $this->assertStringContainsString($this->settings->getSecret(), $body, 'secret does not match');
        $this->assertStringContainsString($this->settings->getRedirectUrl(), $body, 'redirect url does not match');
        $this->assertStringContainsString($this->settings->getState(), $body, 'state does not match');
    }

    public function test_list_webhook_settings()
    {
        $this->markTestSkipped();
        $saveUseCase = $this->createMock(GetGitlabSettings::class);
        $saveUseCase->expects($this->once())
            ->method('list')
            ->willReturn($this->settings);

        $this->container->set(GetGitlabSettings::class, $saveUseCase);

        $response = $this->createRequest('GET', self::SETTINGS_URI);
        $body = (string) $response->getBody();

        $this->assertStringContainsString($this->webhook->getUrl(), $body, 'webhook url does not match');
    }
}