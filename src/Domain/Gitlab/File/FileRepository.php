<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\File;

use App\Domain\Gitlab\Entity\File;

interface FileRepository
{

    public function findFile(int $projectId, string $fileAndPath, string $branchName): File;

    public function update(int $projectId, string $file, array $params): array;

    public function bulkUpdate(int $projectId, array $files, string $branchName, string $commitMessage): array;
}