<?php
declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Persistence\Gitlab;

use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Entity\Webhook;
use App\Infrastructure\Persistence\Gitlab\SettingsFilesystemRepository;
use Tests\Feature\AppTest;

class SettingsFilesystemRepositoryTest extends AppTest
{

    private SettingsFilesystemRepository $filesystemRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->filesystemRepository = $this->container->get(SettingsFilesystemRepository::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->filesystemRepository = $this->container->get(SettingsFilesystemRepository::class);
    }

    public function test_store_settings()
    {
        $settingsToCache = new Settings(
            'http://gilab.com',
            'my_client',
            'my_secret',
            'http://localhost',
            'my_state',
        );

        $webhookToCache = new Webhook(
            'http://webhook.com',
            '123',
            true,
            true
        );

        $this->filesystemRepository->store($settingsToCache, $webhookToCache);

        $cachedSettings = $this->filesystemRepository->get();

        $this->assertEquals($settingsToCache, $cachedSettings);
    }
}