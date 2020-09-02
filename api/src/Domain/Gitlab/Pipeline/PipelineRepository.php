<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Pipeline;

interface PipelineRepository
{

    public function trigger(int $projectId, string $branch, array $variables = []): bool;
}