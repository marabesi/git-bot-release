<?php
declare(strict_types=1);

namespace App\Web;

use App\Web\Actions\Gitlab\Auth\Authorized;
use App\Web\Actions\Gitlab\Auth\RequestToken;
use App\Web\Actions\Gitlab\Auth\Unauthorized;
use App\Web\Actions\Gitlab\Settings\Get;
use App\Web\Actions\Gitlab\Settings\Save;
use App\Web\Actions\Gitlab\Webhook\Income;
use App\Web\Actions\Gitlab\Welcome;

class Routes
{

    private const ALLOWED_ROUTES = [
        ['/', Welcome::class, 'welcome', 'get'],
        ['/request-token', RequestToken::class, 'request-token', 'get'],
        ['/unauthorized', Unauthorized::class, 'unauthorized', 'get'],
        ['/hook/income', Income::class, 'income', 'post'],
        ['/settings', Get::class, 'get_settings', 'get'],
        ['/settings', Save::class, 'settings', 'post'],
    ];

    private const PROTECTED_ROUTES = [
        ['/authorized', Authorized::class, 'authorized', 'get'],
    ];

    public static function getAllowedRoutes(): array
    {
        return array_map(fn($route) => $route[0], self::ALLOWED_ROUTES);
    }

    public static function getAll(): array
    {
        return array_merge(
            self::ALLOWED_ROUTES,
            self::PROTECTED_ROUTES
        );
    }
}