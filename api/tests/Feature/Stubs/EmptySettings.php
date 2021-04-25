<?php

namespace Tests\Feature\Stubs;

use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Project\SettingsRepository;

class EmptySettings implements SettingsRepository
{

    public function store(Settings $settings): bool
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
}