<?php
declare(strict_types=1);

namespace App\UseCases\Gitlab\Project;

use App\Domain\Gitlab\Entity\Webhook;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use App\Domain\Gitlab\Project\ProjectsRepository;

class Details
{
    private ProjectsRepository $projectRepository;
    private Webhook $webhook;
    private FilesToReleaseRepository $filesToReleaseRepository;

    public function __construct(
        ProjectsRepository $projectsRepository,
        Webhook $webhook,
        FilesToReleaseRepository $filesToReleaseRepository
    )
    {
        $this->projectRepository = $projectsRepository;
        $this->webhook = $webhook;
        $this->filesToReleaseRepository = $filesToReleaseRepository;
    }

    public function fetchForProject(int $id): array
    {
        $projectInformation = $this->projectRepository->fetchProject($id);
        $projectWebhooks = $this->projectRepository->fetchWebhooks($id);

        $alreadyRegistered = false;
        $currentWebhook = [];

        foreach ($projectWebhooks as $projectHook) {
            if ($projectHook['url'] === $this->webhook->getUrl()) {
                $alreadyRegistered = true;
                $currentWebhook = $projectHook;
                break;
            }
        }

        return [
            'project' => $projectInformation,
            'webhook' => $currentWebhook,
            'already_registered' => $alreadyRegistered,
            'files' => $this->filesToReleaseRepository->findAll($id),
        ];
    }
}