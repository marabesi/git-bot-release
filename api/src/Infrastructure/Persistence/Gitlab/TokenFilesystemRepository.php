<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Gitlab;

use App\Domain\Gitlab\Authentication\CouldNotEraseTokenException;
use App\Domain\Gitlab\Authentication\CouldNotStoreTokenException;
use App\Domain\Gitlab\Authentication\TokenNotFound;
use App\Domain\Gitlab\Authentication\TokenRepository;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

class TokenFilesystemRepository implements TokenRepository
{

    private const CACHE_KEY = 'token_';

    private FilesystemAdapter $filesystemPool;

    public function __construct(FilesystemAdapter $filesystemCachePool)
    {
        $this->filesystemPool = $filesystemCachePool;
    }

    public function storeToken(string $token): bool
    {
        /** @var $token CacheItem */
        $item = $this->filesystemPool->getItem(self::CACHE_KEY);
        $item->set($token);

        if ($this->filesystemPool->save($item)) {
            return true;
        }

        throw new CouldNotStoreTokenException('could not store token');
    }

    /**
     * @throws TokenNotFound
     * @throws InvalidArgumentException
     */
    public function getToken(): string
    {
        /** @var $token CacheItem */
        $token = $this->filesystemPool->getItem(self::CACHE_KEY);
        $found = (string) $token->get();

        if (!$found) {
            throw new TokenNotFound();
        }

        return $found;
    }

    public function deleteToken(): bool
    {
        if ($this->filesystemPool->delete(self::CACHE_KEY)) {
            return true;
        }

        throw new CouldNotEraseTokenException();
    }
}