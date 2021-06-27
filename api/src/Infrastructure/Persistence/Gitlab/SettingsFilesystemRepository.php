<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Gitlab;

use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Entity\Webhook;
use App\Domain\Gitlab\Project\SettingsRepository;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\CacheItem;

class SettingsFilesystemRepository implements SettingsRepository
{

    private const CACHE_KEY = 'setting';
    private FilesystemAdapter $adapter;

    public function __construct(FilesystemAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function store(Settings $settings, Webhook $webhook): bool
    {
        $cache = $this->adapter->getItem(self::CACHE_KEY);
        $cache->set($settings);

        return $this->adapter->save($cache);
    }

    public function get(): Settings
    {
        /** @var CacheItem $cached */
        $cached = $this->adapter->getItem(self::CACHE_KEY);
        $settings = $cached->get();

        if ($settings) {
            return $settings;
        }

        return new Settings(
            '',
            '',
            '',
            '',
            '',
        );
    }

    public function delete(): bool
    {
        return $this->adapter->delete(self::CACHE_KEY);
    }
}