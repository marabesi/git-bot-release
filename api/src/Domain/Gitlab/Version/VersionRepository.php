<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Version;

use App\Infrastructure\Gateway\Gitlab\Exception\FailedToFetchVersion;

interface VersionRepository
{

    /**
     * @return array
     * @throws FailedToFetchVersion
     */
    public function fetchCurrent(): array;
}