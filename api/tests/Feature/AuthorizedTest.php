<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Gitlab\Authentication\TokenRepository;
use App\Domain\Gitlab\Project\ProjectsRepository;
use App\Domain\Gitlab\Version\VersionRepository;
use Tests\Feature\Stubs\WithExpiredFakeToken;
use Tests\Feature\Stubs\WithFakeToken;
use Tests\Feature\Stubs\WithFakeVersion;

class AuthorizedTest extends AppTest
{

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

    public function test_should_delete_token_if_expired()
    {
        $tokenRepository = $this->createMock(TokenRepository::class);
        $tokenRepository->expects($this->at(0))
            ->method('getToken')
            ->willReturn('fake_expired_token');
        $tokenRepository->expects($this->at(1))
            ->method('getToken')
            ->willReturn('');
        $tokenRepository->expects($this->once())
            ->method('deleteToken')
            ->willReturn(true);
        $this->container->set(TokenRepository::class, $tokenRepository);
        $this->container->set(VersionRepository::class, new WithExpiredFakeToken());

        $response = $this->createRequest('GET', '/');

        $this->assertEmpty($response->getHeaderLine('Location'));
    }
}
