<?php
declare(strict_types=1);

namespace App\UseCases\Gitlab\Settings;

use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Project\SettingsRepository;
use JetBrains\PhpStorm\ArrayShape;

class GetGitlabSettings
{

    private SettingsRepository $repository;

    public function __construct(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    #[ArrayShape(['gitlab' => Settings::class])]
    public function list(): array
    {
        return [
            'gitlab' => $this->repository->get(),
        ];
    }
}