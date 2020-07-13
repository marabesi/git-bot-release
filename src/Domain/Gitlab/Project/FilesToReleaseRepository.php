<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Project;

use App\Domain\Gitlab\Entity\File;

interface FilesToReleaseRepository
{

    public function save(int $projectId, File $file): bool;

    public function delete(int $projectId, File $file): bool;

    public function findAll(int $projectId): array;
}