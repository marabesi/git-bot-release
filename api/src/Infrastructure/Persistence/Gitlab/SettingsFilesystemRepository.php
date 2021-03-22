<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Gitlab;

use App\Domain\Gitlab\Entity\Settings;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

class SettingsFilesystemRepository
{

    private const CACHE_KEY = 'settings';
    private FilesystemAdapter $adapter;

    public function __construct(FilesystemAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function store(Settings $settings)
    {
        $cache = new CacheItem();
        $cache->set($settings);

        return $this->adapter->save($cache);
    }

    public function get(): Settings
    {
        /** @var CacheItem $cached */
        $cached = $this->adapter->getItem(self::CACHE_KEY);
        return $cached->get();
    }
}