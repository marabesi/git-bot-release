<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Webhook;

use App\Domain\Gitlab\Entity\Webhook;

interface WebhookRepository
{

    public function registerWebhook(int $projectId, Webhook $webHook): bool;

    public function deleteWebhook(int $projectId, int $id): bool;
}