<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Tag;

interface TagRepository
{

    public function createTag(int $projectId, string $name, string $fromBranch);
}