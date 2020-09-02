<?php
declare(strict_types=1);

namespace App\Infrastructure\Gateway\Gitlab;

use App\Domain\Gitlab\Entity\Webhook;
use App\Domain\Gitlab\Webhook\WebhookRepository;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use Exception;

class WebHookApiRepository implements WebhookRepository
{

    private NetworkRequestAuthenticated $networkRequestAuthenticated;

    public function __construct(NetworkRequestAuthenticated $networkRequestAuthenticated)
    {
        $this->networkRequestAuthenticated = $networkRequestAuthenticated;
    }

    public function registerWebhook(int $projectId, Webhook $webHook): bool
    {
        $url = sprintf('api/v4/projects/%s/hooks', $projectId);
        $posted = $this->networkRequestAuthenticated->post($url, [
            'url' => $webHook->getUrl(),
            'token' => $webHook->getToken(),
            'push_events' => $webHook->getPushEvents(),
            'enable_ssl_verification' => $webHook->getEnableSslVerification(),
        ]);

        if (count($posted) > 1) {
            return true;
        }

        throw new Exception('Error trying to push the new webhook');
    }

    public function deleteWebhook(int $projectId, int $id): bool
    {
        $url = sprintf('api/v4/projects/%s/hooks/%s', $projectId, $id);
        $posted = $this->networkRequestAuthenticated->delete($url);

        if (count($posted) === 0) {
            return true;
        }

        throw new Exception('Error trying to delete the webhook ' . $id);
    }
}