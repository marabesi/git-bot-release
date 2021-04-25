<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Project;

use App\Domain\Gitlab\Entity\Settings;

interface SettingsRepository
{

    public function store(Settings $settings): bool;

    public function get(): Settings;
}