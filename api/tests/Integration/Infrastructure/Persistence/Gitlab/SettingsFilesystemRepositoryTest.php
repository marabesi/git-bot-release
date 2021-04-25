<?php
declare(strict_types=1);

namespace Tests\Integration\Infrastructure\Persistence\Gitlab;

use App\Domain\Gitlab\Entity\Settings;
use App\Infrastructure\Persistence\Gitlab\SettingsFilesystemRepository;
use PHPUnit\Framework\TestCase;
use Tests\Feature\AppTest;

class SettingsFilesystemRepositoryTest extends TestCase
{
    use AppTest;

    public function test_store_settings()
    {
        $filesystemRepository = $this->container->get(SettingsFilesystemRepository::class);

        $toCache = new Settings(
            'http://gilab.com',
            'my_client',
            'my_secret',
            'http://localhost',
            'my_state',
        );
        $filesystemRepository->store($toCache);

        $cachedSettings = $filesystemRepository->get();

        $this->assertEquals($toCache, $cachedSettings);
    }
}