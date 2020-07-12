<?php
declare(strict_types=1);

use Slim\App;
use App\Application\Actions\Gitlab\RequestToken;
use App\Application\Actions\Gitlab\Unauthorized;
use App\Application\Actions\Gitlab\Welcome;
use App\Application\Actions\Gitlab\Authorized;

return function (App $app) {
    $app->get('/', Welcome::class)
        ->setName('welcome');
    $app->get('/request-token', RequestToken::class)
        ->setName('request-token');
    $app->get('/unauthorized', Unauthorized::class)
        ->setName('unauthorized');
    $app->get('/authorized', Authorized::class)
        ->setName('authorized');
};
