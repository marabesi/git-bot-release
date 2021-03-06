<?php
declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Persistence\Gitlab;

use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Entity\Webhook;
use PHPUnit\Framework\TestCase;
use App\Infrastructure\Persistence\Gitlab\SettingsFilesystemRepository;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

class SettingsFilesystemRepositoryTest extends TestCase
{

    private Settings $settings;
    private Webhook $webhook;

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

        $this->webhook = new Webhook(
            'http://webhook.com',
            '123',
            true,
            true
        );
    }

    public function test_store_gitlab_settings_and_webhook()
    {
        $adapter = $this->createMock(FilesystemAdapter::class);
        $adapter->expects($this->exactly(2))
            ->method('save')
            ->willReturn(true);
        $adapter->expects($this->exactly(2))
            ->method('getItem')
            ->willReturn(new CacheItem());

        $repository = new SettingsFilesystemRepository($adapter);
        $this->assertTrue(
            $repository->store($this->settings, $this->webhook)
        );
    }

    public function test_get_gitlab_settings()
    {
        $cachedItem = new CacheItem();
        $cachedItem->set($this->settings);

        $adapter = $this->createMock(FilesystemAdapter::class);
        $adapter->expects($this->once())
            ->method('getItem')
            ->willReturn($cachedItem);

        $repository = new SettingsFilesystemRepository($adapter);
        $this->assertSame(
            $this->settings,
            $repository->get()
        );
    }

    public function test_get_webhook_settings()
    {
        $cachedItem = new CacheItem();
        $cachedItem->set($this->webhook);

        $adapter = $this->createMock(FilesystemAdapter::class);
        $adapter->expects($this->once())
            ->method('getItem')
            ->willReturn($cachedItem);

        $repository = new SettingsFilesystemRepository($adapter);
        $this->assertSame(
            $this->webhook,
            $repository->getWebhook()
        );
    }

    public function test_return_gitlab_empty_settings_if_does_not_exists()
    {
        $cachedItem = new CacheItem();

        $adapter = $this->createMock(FilesystemAdapter::class);
        $adapter->expects($this->once())
            ->method('getItem')
            ->willReturn($cachedItem);

        $repository = new SettingsFilesystemRepository($adapter);
        $storedSettings = $repository->get();

        $this->assertEquals('', $storedSettings->getGitlabUrl());
        $this->assertEquals('', $storedSettings->getClientId());
        $this->assertEquals('', $storedSettings->getSecret());
        $this->assertEquals('', $storedSettings->getRedirectUrl());
        $this->assertEquals('', $storedSettings->getState());
    }

    public function test_return_webhook_empty_settings_if_does_not_exists()
    {
        $cachedItem = new CacheItem();

        $adapter = $this->createMock(FilesystemAdapter::class);
        $adapter->expects($this->once())
            ->method('getItem')
            ->willReturn($cachedItem);

        $repository = new SettingsFilesystemRepository($adapter);
        $storedWebhookSettings = $repository->getWebhook();

        $this->assertEquals('', $storedWebhookSettings->getUrl());
        $this->assertEquals('', $storedWebhookSettings->getToken());
        $this->assertEquals(false, $storedWebhookSettings->getPushEvents());
        $this->assertEquals(false, $storedWebhookSettings->getEnableSslVerification());
    }
}