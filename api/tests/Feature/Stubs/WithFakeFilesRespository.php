<?php

namespace Tests\Feature\Stubs;

use App\Domain\Gitlab\Entity\File;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;

class WithFakeFilesRespository implements FilesToReleaseRepository
{

    public function save(int $projectId, File $file): bool
    {
        return true;
    }

    public function delete(int $projectId, File $file): bool
    {
        return true;
    }

    public function findAll(int $projectId): array
    {
        return [];
    }
}