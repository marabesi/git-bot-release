<?php
declare(strict_types=1);

namespace App\UseCases\Gitlab\Settings;

use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Project\SettingsRepository;

class GetGitlabSettings
{

    private SettingsRepository $repository;

    public function __construct(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list(): Settings
    {
        return $this->repository->get();
    }
}