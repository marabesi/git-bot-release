<?php
declare(strict_types=1);

namespace App\UseCases\Gitlab\Settings;

use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Project\SettingsRepository;

class SaveGitlabSettings
{
    private SettingsRepository $repository;

    public function __construct(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function save(Settings $setting): bool
    {
        return $this->repository->store($setting);
    }
}