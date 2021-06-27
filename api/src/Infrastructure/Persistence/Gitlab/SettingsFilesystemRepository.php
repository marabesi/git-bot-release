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

    private const SETTINGS_CACHE_KEY = 'setting';
    private const WEBHOOK_CACHE_KEY = 'webhook';

    private FilesystemAdapter $adapter;

    public function __construct(FilesystemAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function store(Settings $settings, Webhook $webhook): bool
    {
        $cache = $this->adapter->getItem(self::SETTINGS_CACHE_KEY);
        $cache->set($settings);

        $settingsSaved = $this->adapter->save($cache);

        $cache = $this->adapter->getItem(self::WEBHOOK_CACHE_KEY);
        $cache->set($webhook);

        $webhookSaved = $this->adapter->save($cache);

        return $settingsSaved && $webhookSaved;
    }

    public function get(): Settings
    {
        /** @var CacheItem $cached */
        $cached = $this->adapter->getItem(self::SETTINGS_CACHE_KEY);
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

    public function getWebhook(): Webhook
    {
        /** @var CacheItem $cached */
        $cached = $this->adapter->getItem(self::WEBHOOK_CACHE_KEY);
        $webhook = $cached->get();

        if ($webhook) {
            return $webhook;
        }

        return new Webhook(
            '',
            '',
             false,
            false
        );
    }

    public function delete(): bool
    {
        return $this->adapter->delete(self::SETTINGS_CACHE_KEY);
    }
}