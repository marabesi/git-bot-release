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

    public function test_bulk_update_files()
    {
        $actions = [
            'actions' => [
                [
                    'action' => 'update',
                    'file_path' => 'src/',
                    'content' => 'my content',
                ]
            ]
        ];

        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('post')
            ->willReturn([
                'updated'
            ]);
        $repository = new FileApiRepository($authenticatedRequest);
        $response = $repository->bulkUpdate(
            1,
            $actions,
            'main',
            'chore: bulk update'
        );

        $this->assertEquals('updated', $response[0]);
    }

    public function test_find_a_file_from_a_project()
    {
        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('get')
            ->willReturn([
                'blob_id' => 'fake_blob_id',
                'file_name' => 'fake_file.txt',
                'file_path' => '/src',
                'content' => base64_encode('myfilecontent'),
            ]);
        $repository = new FileApiRepository($authenticatedRequest);
        $response = $repository->findFile(
            1,
            '/src/fake_file.txt',
            'main',
        );
        $this->assertEquals('fake_file.txt', $response->getName());
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