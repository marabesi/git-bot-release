<?php

namespace Tests\Feature\Stubs;

use App\Domain\Gitlab\Authentication\TokenRevoked;
use App\Domain\Gitlab\Version\VersionRepository;

class WithExpiredFakeToken implements VersionRepository
{
    public function fetchCurrent(): array
    {
        throw new TokenRevoked();
    }
}