<?php

namespace Tests\Unit\UseCases\Gitlab;

use App\Domain\Gitlab\Project\ProjectsRepository;
use App\UseCases\Gitlab\ProjectList;
use PHPUnit\Framework\TestCase;

class ProjectListTest extends TestCase
{

    public function test_list_available_projects()
    {
        $projectRepository = $this->createMock(ProjectsRepository::class);
        $projectRepository->expects($this->once())
            ->method('fetchProjects');

        $useCase = new ProjectList($projectRepository);
        $projects = $useCase->fetch();

        $this->assertArrayHasKey('projects', $projects);
    }
}