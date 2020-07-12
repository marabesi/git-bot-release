<?php

namespace App\Infrastructure\Persistence\Gitlab;

use App\Domain\Gitlab\GitlabRepository;
use Cache\Adapter\Common\CacheItem;
use Cache\Adapter\Filesystem\FilesystemCachePool;

class FilesystemRepository implements GitlabRepository
{

    private const CACHE_KEY = 'token';

    private FilesystemCachePool $filesystemPool;

    public function __construct(FilesystemCachePool $filesystemCachePool)
    {
        $this->filesystemPool = $filesystemCachePool;
    }

    public function storeToken(string $token): bool
    {
        $item = new CacheItem(self::CACHE_KEY, null, $token);
        return $this->filesystemPool->save($item);
    }

    public function getToken(): string
    {
        return (string) $this->filesystemPool->get(self::CACHE_KEY);
    }
}