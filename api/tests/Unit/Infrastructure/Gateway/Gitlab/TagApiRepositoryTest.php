<?php
declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Gateway\Gitlab;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Gateway\Gitlab\TagApiRepository;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use App\Infrastructure\Gateway\Gitlab\Exception\FailedToCreateTag;

class TagApiRepositoryTest extends TestCase
{

    public function test_create_tag_for_a_given_gitlab_project()
    {
        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('post')
            ->with('api/v4/projects/1/repository/tags', [
                'tag_name' => 'v1.0.0',
                'ref' => 'master',
            ])
            ->willReturn([
                'commit' => true,
            ]);
        $repository = new TagApiRepository($authenticatedRequest);
        $repository->createTag(1, 'v1.0.0', 'master');
    }

    public function test_throw_exception_when_gitlab_response_has_no_commit_in_the_response()
    {
        $this->expectException(FailedToCreateTag::class);
        $this->expectExceptionMessage('error while posting new tag');

        $authenticatedRequest = $this->createMock(NetworkRequestAuthenticated::class);
        $authenticatedRequest->expects($this->once())
            ->method('post')
            ->willReturn([
                'message' => 'error while posting new tag'
            ]);
        $repository = new TagApiRepository($authenticatedRequest);
        $repository->createTag(1, 'v1.0.0', 'master');
    }
}