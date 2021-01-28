<?php

namespace App\Web\Actions\Gitlab\Webhook;

use App\Domain\Gitlab\Webhook\WebhookRepository;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class Delete
{

    private Twig $twig;
    private WebhookRepository $webhookRepository;

    public function __construct(Twig $twig, WebhookRepository $webhookRepository)
    {
        $this->twig = $twig;
        $this->webhookRepository = $webhookRepository;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $projectId = $args['id'];
        $id = $args['hookId'];

        $this->webhookRepository->deleteWebhook((int) $projectId, (int) $id);

        return $response->withHeader('Location', sprintf('/projects/%s/detail', $projectId));
    }
}