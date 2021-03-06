<?php

namespace Tests\Feature\Settings;

use App\Domain\Gitlab\Entity\Settings;
use App\Infrastructure\Persistence\Gitlab\SettingsFilesystemRepository;
use App\UseCases\Gitlab\Settings\GetGitlabSettings;
use Tests\Feature\AppTest;

class GetGitlabSettingTest extends AppTest
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

    public function test_list_stored_settings()
    {
        $repository = $this->createMock(SettingsFilesystemRepository::class);
        $repository->expects($this->once())
            ->method('get')
            ->willReturn($this->settings);

        $useCase = new GetGitlabSettings($repository);
        $this->assertSame(
            $this->settings,
            $useCase->list()['gitlab'],
            'The settings saved and the settings retrieved are no the same'
        );
    }

}