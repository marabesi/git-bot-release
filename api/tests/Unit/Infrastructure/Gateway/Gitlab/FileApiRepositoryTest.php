<?php

namespace Tests\Unit\Infrastructure\Gateway\Gitlab;

use App\Infrastructure\Gateway\Gitlab\Exception\CouldNotUpdateFile;
use App\Infrastructure\Gateway\Gitlab\FileApiRepository;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use PHPUnit\Framework\TestCase;

class FileApiRepositoryTest extends TestCase
{

    public function test_update_file_from_a_project()
    {
        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('put')
            ->willReturn([ 'my updated file' ]);
        $repository = new FileApiRepository($authenticatedRequest);
        $response = $repository->update(
            1,
            'myfile.js',
            []
        );
        $this->assertEquals('my updated file', $response[0]);
    }

    public function test_throw_exception_when_gitlab_api_has_message_key_in_the_response()
    {
        $this->expectException(CouldNotUpdateFile::class);

        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('put')
            ->willReturn([
                'message' => 'something went wrong'
            ]);
        $repository = new FileApiRepository($authenticatedRequest);
        $repository->update(
            1,
            'myfile.js',
            []
        );
    }
}