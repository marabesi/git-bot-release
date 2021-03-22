<?php
declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Persistence\Gitlab;

use App\Domain\Gitlab\Authentication\CouldNotEraseTokenException;
use App\Domain\Gitlab\Authentication\CouldNotStoreTokenException;
use App\Domain\Gitlab\Authentication\TokenNotFound;
use App\Infrastructure\Persistence\Gitlab\TokenFilesystemRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

class TokenFilesystemRepositoryTest extends TestCase
{

    private FileSystemAdapter $adapter;
    private TokenFilesystemRepository $repository;
    private const TOKEN = 'MY_PERSONAL_TOKEN';

    public function setUp(): void
    {
        parent::setUp();
        $this->adapter = $this->createMock(FilesystemAdapter::class);
        $this->repository = new TokenFilesystemRepository($this->adapter);
    }

    public function test_store_token()
    {
        $cached = new CacheItem();

        $this->adapter->expects($this->once())
            ->method('getItem')
            ->willReturn($cached);
        $this->adapter->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $this->repository->storeToken(self::TOKEN);
    }

    public function test_error_when_saving_token()
    {
        $this->expectException(CouldNotStoreTokenException::class);

        $cached = new CacheItem();

        $this->adapter->expects($this->once())
            ->method('getItem')
            ->willReturn($cached);
        $this->adapter->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $this->repository->storeToken(self::TOKEN);
    }

    public function test_delete_token()
    {
        $this->adapter->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $this->assertTrue(
            $this->repository->deleteToken()
        );
    }

    public function test_error_while_deleting_token()
    {
        $this->expectException(CouldNotEraseTokenException::class);

        $this->adapter->expects($this->once())
            ->method('delete')
            ->willReturn(false);

        $this->repository->deleteToken();
    }

    public function test_error_trying_to_fetch_token()
    {
        $this->expectException(TokenNotFound::class);

        $cached = new CacheItem();

        $this->adapter->expects($this->once())
            ->method('getItem')
            ->willReturn($cached);

        $this->repository->getToken();
    }
}