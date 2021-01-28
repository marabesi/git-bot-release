<?php

namespace Tests\Unit\UseCases\Gitlab\Settings;

use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
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
        $repository = $this->createMock(FilesToReleaseRepository::class);
        $repository->expects($this->once())
            ->method('save');
        $useCase = new SaveGitlabSettings($repository);
        $useCase->save($this->settings);
    }
}