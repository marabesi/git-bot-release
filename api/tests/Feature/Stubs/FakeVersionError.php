<?php

namespace Tests\Feature\Stubs;

use App\Domain\Gitlab\Version\VersionRepository;
use App\Infrastructure\Gateway\Gitlab\Exception\FailedToFetchVersion;

class FakeVersionError implements VersionRepository
{
    public function fetchCurrent(): array
    {
        throw new FailedToFetchVersion('failed to fetch from stub class');
    }
}