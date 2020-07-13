<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Version;

interface VersionRepository
{

    public function fetchCurrent(): array;
}