<?php

namespace Tests\Feature;

use App\Domain\Gitlab\Authentication\TokenRepository;
use App\Domain\Gitlab\Project\ProjectsRepository;
use App\Domain\Gitlab\Version\VersionRepository;
use PHPUnit\Framework\TestCase;
use Tests\Feature\Stubs\WithFakeToken;
use Tests\Feature\Stubs\WithFakeVersion;

class AuthorizedTest extends TestCase
{
    use AppTest;

    public function test_list_projects()
    {
        $projectRepository = $this->createMock(ProjectsRepository::class);
        $projectRepository->expects($this->once())
            ->method('fetchProjects')
            ->willReturn([
                [
                    'id' => 1,
                    'name' => 'Fake project'
                ]
            ]);

        $this->container->set(ProjectsRepository::class, $projectRepository);
        $this->container->set(TokenRepository::class, new WithFakeToken());
        $this->container->set(VersionRepository::class, new WithFakeVersion());

        $response = $this->createRequest('GET', '/authorized');
        $this->assertStringContainsString('<a href="/projects/1/detail">Fake project</a>', (string) $response->getBody());
    }
}
