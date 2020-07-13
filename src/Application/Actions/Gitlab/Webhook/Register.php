<?php

namespace App\Application\Actions\Gitlab\Webhook;

use App\Domain\Gitlab\Entity\Webhook;
use App\Domain\Gitlab\Webhook\WebhookRepository;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class Register
{

    private Twig $twig;
    private WebhookRepository $webhookRepository;
    private Webhook $webhook;

    public function __construct(Twig $twig, WebhookRepository $webhookRepository, Webhook $webhook)
    {
        $this->twig = $twig;
        $this->webhookRepository = $webhookRepository;
        $this->webhook = $webhook;
    }

    public function __invoke(Request $request, Response $response, array $args)
    {
        $projectId = $args['id'];

        $this->webhookRepository->registerWebhook((int) $projectId, $this->webhook);

        return $response->withHeader('Location', sprintf('/projects/%s/detail', $projectId));
    }
}