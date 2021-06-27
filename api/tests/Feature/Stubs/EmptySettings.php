<?php

namespace Tests\Feature\Stubs;

use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Entity\Webhook;
use App\Domain\Gitlab\Project\SettingsRepository;

class EmptySettings implements SettingsRepository
{

    public function store(Settings $settings, Webhook $webhook): bool
    {
        return true;
    }

    public function get(): Settings
    {
        return new Settings(
            '',
            '',
            '',
            '',
            '',
        );
    }

    public function delete(): bool
    {
        return true;
    }
}