<?php

namespace Tests\Feature\Stubs;

use App\Domain\Gitlab\Version\VersionRepository;

class WithFakeVersion implements VersionRepository
{
    public function fetchCurrent(): array
    {
        return [];
    }
}