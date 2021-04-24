<?php
declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Persistence\Gitlab;

use App\Domain\Gitlab\Entity\Settings;
use PHPUnit\Framework\TestCase;
use App\Infrastructure\Persistence\Gitlab\SettingsFilesystemRepository;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

class SettingsFilesystemRepositoryTest extends TestCase
{

    private Settings $settings;

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
    }

    public function test_store_settings()
    {
        $adapter = $this->createMock(FilesystemAdapter::class);
        $adapter->expects($this->once())
            ->method('save')
            ->willReturn(true);
        $adapter->expects($this->once())
            ->method('getItem')
            ->willReturn(new CacheItem());

        $repository = new SettingsFilesystemRepository($adapter);
        $this->assertTrue(
            $repository->store($this->settings)
        );
    }

    public function test_get_settings()
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

    public function test_return_empty_settings_if_does_not_exists()
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
}