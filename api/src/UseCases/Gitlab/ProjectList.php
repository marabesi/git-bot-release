<?php

namespace App\UseCases\Gitlab;

use App\Domain\Gitlab\Project\ProjectsRepository;

class ProjectList
{

    private ProjectsRepository $projectRepository;

    public function __construct(ProjectsRepository $projectsRepository)
    {
        $this->projectRepository = $projectsRepository;
    }

    public function fetch() : array
    {
        return [
            'projects' => $this->projectRepository->fetchProjects(),
        ];
    }
}