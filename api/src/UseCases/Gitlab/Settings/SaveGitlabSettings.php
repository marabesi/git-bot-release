<?php
declare(strict_types=1);

namespace App\UseCases\Gitlab\Settings;

use App\Domain\Gitlab\Entity\Settings;
use App\Infrastructure\Persistence\Gitlab\SettingsFilesystemRepository;

class SaveGitlabSettings
{
    private SettingsFilesystemRepository $repository;

    public function __construct(SettingsFilesystemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function save(Settings $setting): bool
    {
        return $this->repository->store($setting);
    }
}