<?php
declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway\Gitlab;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Gateway\Gitlab\VersionApiRepository;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use App\Infrastructure\Gateway\Gitlab\Exception\FailedToFetchVersion;

class VersionApiRepositoryTest extends TestCase
{

    public function test_fetch_gitlab_server_version()
    {
        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('get')
            ->with('api/v4/version')
            ->willReturn([
                'version' => '8.13',
                'revision' => '123sd12',
            ]);
        $repository = new VersionApiRepository($authenticatedRequest);
        $repository->fetchCurrent();
    }

    public function test_throw_exception_on_error_fetching_current_gitlab_version()
    {
        $this->expectException(FailedToFetchVersion::class);

        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('get')
            ->with('api/v4/version')
            ->willReturn([
                'error_description' => 'error while fetching version',
            ]);
        $repository = new VersionApiRepository($authenticatedRequest);
        $repository->fetchCurrent();
    }
}