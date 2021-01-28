<?php
declare(strict_types=1);

namespace App\Infrastructure\Gateway\Gitlab;

use App\Domain\Gitlab\Version\VersionRepository;
use App\Infrastructure\Gateway\Gitlab\Exception\FailedToFetchVersion;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;

class VersionApiRepository implements VersionRepository
{

    private NetworkRequestAuthenticated $networkRequest;

    public function __construct(NetworkRequestAuthenticated $networkRequestAuthenticated)
    {
        $this->networkRequest = $networkRequestAuthenticated;
    }

    public function fetchCurrent(): array
    {
        $version = $this->networkRequest->get('api/v4/version');

        if (array_key_exists('error_description', $version)) {
            throw new FailedToFetchVersion($version['error_description']);
        }

        return $version;
    }
}