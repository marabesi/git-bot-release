<?php

namespace App\UseCases\Gitlab\Settings;

use App\Domain\Gitlab\Entity\Settings;
use App\Infrastructure\Persistence\Gitlab\SettingsFilesystemRepository;

class GetGitlabSettings
{

    private SettingsFilesystemRepository $repository;

    public function __construct(SettingsFilesystemRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(): Settings
    {
        return $this->repository->get();
    }
}