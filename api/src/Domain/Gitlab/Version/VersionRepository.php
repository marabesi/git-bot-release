<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Version;

use App\Domain\Gitlab\Authentication\TokenRevoked;
use App\Infrastructure\Gateway\Gitlab\Exception\FailedToFetchVersion;

interface VersionRepository
{

    /**
     * @return array
     * @throws FailedToFetchVersion
     * @throws TokenRevoked
     */
    public function fetchCurrent(): array;
}