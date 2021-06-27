<?php
declare(strict_types=1);

namespace Tests\Feature\Settings;

use App\Domain\Gitlab\Authentication\TokenRepository;
use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Entity\Webhook;
use App\Domain\Gitlab\Version\VersionRepository;
use App\UseCases\Gitlab\Settings\GetGitlabSettings;
use App\UseCases\Gitlab\Settings\SaveGitlabSettings;
use Tests\Feature\AppTest;
use Tests\Feature\Stubs\WithFakeToken;
use Tests\Feature\Stubs\WithFakeVersion;

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
            '999-token',
            true,
            true
        );
    }

    public function test_save_settings()
    {
        $this->container->set(TokenRepository::class, new WithFakeToken());
        $this->container->set(VersionRepository::class, new WithFakeVersion());

        $saveUseCase = $this->createMock(SaveGitlabSettings::class);
        $saveUseCase->expects($this->once())
            ->method('save');

        $this->container->set(SaveGitlabSettings::class, $saveUseCase);

        $response = $this->post(self::SETTINGS_URI, [
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

    public function test_list_gitlab_settings_and_webhook()
    {
        $saveUseCase = $this->createMock(GetGitlabSettings::class);
        $saveUseCase->expects($this->once())
            ->method('list')
            ->willReturn([
                'gitlab' => $this->settings,
                'webhook' => $this->webhook
            ]);

        $this->container->set(GetGitlabSettings::class, $saveUseCase);

        $response = $this->createRequest('GET', self::SETTINGS_URI);
        $body = (string) $response->getBody();

        $this->assertStringContainsString($this->settings->getGitlabUrl(), $body, 'gitlab url does not match');
        $this->assertStringContainsString($this->settings->getClientId(), $body, 'client id does not match');
        $this->assertStringContainsString($this->settings->getSecret(), $body, 'secret does not match');
        $this->assertStringContainsString($this->settings->getRedirectUrl(), $body, 'redirect url does not match');
        $this->assertStringContainsString($this->settings->getState(), $body, 'state does not match');

        $this->assertStringContainsString($this->webhook->getUrl(), $body, 'webhook url does not match');
        $this->assertStringContainsString($this->webhook->getToken(), $body, 'webhook token does not match');
        $this->assertStringContainsString(sprintf('name="webhook_push_events" value="%s"', (string) $this->webhook->getPushEvents()), $body, 'webhook push events does not match');
        $this->assertStringContainsString(sprintf('name="webhook_enable_ssl_verification" value="%s"', (string) $this->webhook->getEnableSslVerification()), $body, 'webhook ssl verification events does not match');
    }
}