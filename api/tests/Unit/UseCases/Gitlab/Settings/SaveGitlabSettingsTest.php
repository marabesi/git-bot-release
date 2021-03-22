<?php

namespace Tests\Unit\UseCases\Gitlab\Settings;

use App\Domain\Gitlab\Entity\Settings;
use App\Infrastructure\Persistence\Gitlab\SettingsFilesystemRepository;
use App\UseCases\Gitlab\Settings\SaveGitlabSettings;
use PHPUnit\Framework\TestCase;

class SaveGitlabSettingsTest extends TestCase
{
    private Settings $settings;

    public function setUp(): void
    {
        parent::setUp();
        $this->settings = new Settings(
            'http://my.gitlab.com',
            'myclient_id',
            'secret',
            'http://redirecto.com',
            'mystate'
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_persist_gitlab_settings()
    {
        $repository = $this->createMock(SettingsFilesystemRepository::class);
        $repository->expects($this->once())
            ->method('store')
            ->willReturn(true);

        $useCase = new SaveGitlabSettings($repository);
        $this->assertTrue(
            $useCase->save($this->settings),
            'Error trying to save settings'
        );
    }
}