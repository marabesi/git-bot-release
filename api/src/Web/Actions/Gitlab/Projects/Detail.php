<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab\Projects;

use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use App\Domain\Gitlab\Project\ProjectsRepository;
use App\Domain\Gitlab\Entity\Webhook;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;

class Detail
{

    private Twig $twig;
    private ProjectsRepository $projectRepository;
    private Webhook $webhook;
    private FilesToReleaseRepository $filesToReleaseRepository;

    public function __construct(
        Twig $twig,
        ProjectsRepository $projectsRepository,
        Webhook $webhook,
        FilesToReleaseRepository $filesToReleaseRepository
    )
    {
        $this->twig = $twig;
        $this->projectRepository = $projectsRepository;
        $this->webhook = $webhook;
        $this->filesToReleaseRepository = $filesToReleaseRepository;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $projectId = (int) $args['id'];

        $projectInformation = $this->projectRepository->fetchProject($projectId);
        $projectWebhooks = $this->projectRepository->fetchWebhooks($projectId);

        $alreadyRegistered = false;
        $currentWebhook = [];

        foreach ($projectWebhooks as $projectHook) {
            if ($projectHook['url'] === $this->webhook->getUrl()) {
                $alreadyRegistered = true;
                $currentWebhook = $projectHook;
                break;
            }
        }

        return $this->twig->render($response,  'templates/projects/detail.twig', [
            'project' => $projectInformation,
            'webhook' => $currentWebhook,
            'already_registered' => $alreadyRegistered,
            'files' => $this->filesToReleaseRepository->findAll($projectId),
        ]);
    }
}