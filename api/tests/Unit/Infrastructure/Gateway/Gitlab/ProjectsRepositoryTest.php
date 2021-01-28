<?php
declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway\Gitlab;

use App\Infrastructure\Gateway\Gitlab\Exception\FailedToFetchProjects;
use App\Infrastructure\Gateway\Gitlab\ProjectsApiRepository;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use PHPUnit\Framework\TestCase;

class ProjectsRepositoryTest extends TestCase
{

    public function test_fetch_owned_projects_from_gitlab()
    {
        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('get')
            ->willReturn([]);
        $repository = new ProjectsApiRepository($authenticatedRequest);
        $this->assertCount(0, $repository->fetchProjects());
    }

    public function test_throw_exception_when_gitlab_returns_error_description()
    {
        $this->expectException(FailedToFetchProjects::class);
        $this->expectExceptionMessage('error response from gitlab');

        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('get')
            ->willReturn([
                'error_description' => 'error response from gitlab'
            ]);

        $repository = new ProjectsApiRepository($authenticatedRequest);
        $repository->fetchProjects();
    }

    public function test_fetch_gitlab_project_by_id()
    {
        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('get')
            ->with('api/v4/projects/1')
            ->willReturn([]);

        $repository = new ProjectsApiRepository($authenticatedRequest);
        $repository->fetchProject(1);
    }

    public function test_fetch_webhooks_from_project()
    {
        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('get')
            ->with('api/v4/projects/1/hooks')
            ->willReturn([]);

        $repository = new ProjectsApiRepository($authenticatedRequest);
        $repository->fetchWebhooks(1);
    }
}