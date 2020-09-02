<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Project;

interface ProjectsRepository
{

    public function fetchProjects(): array;

    public function fetchProject(int $id): array;

    public function fetchWebhooks(int $id): array;
}