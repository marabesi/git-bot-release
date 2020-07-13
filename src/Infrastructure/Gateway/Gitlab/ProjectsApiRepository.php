<?php
declare(strict_types=1);

namespace App\Infrastructure\Gateway\Gitlab;

use App\Domain\Gitlab\Project\ProjectsRepository as Repository;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use Exception;

class ProjectsApiRepository implements Repository
{
    private NetworkRequestAuthenticated $networkRequest;

    public function __construct(NetworkRequestAuthenticated $networkRequestAuthenticated)
    {
        $this->networkRequest = $networkRequestAuthenticated;
    }

    public function fetchProjects(): array
    {
        $projects = $this->networkRequest->get('api/v4/projects', [
            'owned' => 'true',
        ]);

        if (array_key_exists('error_description', $projects)) {
            throw new Exception($projects['error_description']);
        }

        return $projects;
    }

    public function fetchProject(int $id): array
    {
        return $this->networkRequest->get(sprintf('api/v4/projects/%s', $id));
    }

    public function fetchWebhooks(int $id): array
    {
        return $this->networkRequest->get(sprintf('api/v4/projects/%s/hooks', $id));
    }
}