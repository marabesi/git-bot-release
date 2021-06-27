<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Project;

use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Entity\Webhook;

interface SettingsRepository
{

    public function store(Settings $settings, Webhook $webhook): bool;

    public function get(): Settings;

    public function getWebhook(): Webhook;

    public function delete(): bool;
}