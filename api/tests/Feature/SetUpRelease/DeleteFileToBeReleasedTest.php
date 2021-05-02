<?php
declare(strict_types=1);

namespace Tests\Feature\SetUpRelease;

use App\Domain\Gitlab\Authentication\TokenRepository;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use App\Domain\Gitlab\Version\VersionRepository;
use Tests\Feature\AppTest;
use Tests\Feature\Stubs\WithFakeFilesRespository;
use Tests\Feature\Stubs\WithFakeToken;
use Tests\Feature\Stubs\WithFakeVersion;

class DeleteFileToBeReleasedTest extends AppTest
{

    public function setUp(): void
    {
        parent::setUp();
        $this->container->set(TokenRepository::class, new WithFakeToken());
        $this->container->set(VersionRepository::class, new WithFakeVersion());
    }

    public function test_delete_file()
    {
        $this->container->set(FilesToReleaseRepository::class, new WithFakeFilesRespository());

        $response = $this->post('/projects/1/file/1', []);

        $this->assertStringContainsString('/projects/1/detail', $response->getHeaderLine('Location'));
    }
}