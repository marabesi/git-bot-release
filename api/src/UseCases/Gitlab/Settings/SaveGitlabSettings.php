<?php

namespace App\UseCases\Gitlab\Settings;

use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;

class SaveGitlabSettings
{
    private FilesToReleaseRepository $repository;

    public function __construct(FilesToReleaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function save(Settings $setting)
    {
        $content = json_encode($setting);
        $file = new File('settings', 'settings', '/', $content);
        $this->repository->save(0, $file);
    }
}