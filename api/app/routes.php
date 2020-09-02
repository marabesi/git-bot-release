<?php
declare(strict_types=1);

use Slim\App;
use App\Application\Actions\Gitlab\Auth\RequestToken;
use App\Application\Actions\Gitlab\Auth\Unauthorized;
use App\Application\Actions\Gitlab\Auth\Authorized;
use App\Application\Actions\Gitlab\Welcome;
use App\Application\Actions\Gitlab\Projects\Detail;
use App\Application\Actions\Gitlab\Webhook\Register;
use App\Application\Actions\Gitlab\Webhook\Income;
use App\Application\Actions\Gitlab\Webhook\Delete;
use App\Application\Actions\Gitlab\File\Create;
use App\Application\Actions\Gitlab\File\Delete as FileDelete;

use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    $app->get('/', Welcome::class)->setName('welcome');
    $app->get('/request-token', RequestToken::class)->setName('request-token');
    $app->get('/unauthorized', Unauthorized::class)->setName('unauthorized');
    $app->get('/authorized', Authorized::class)->setName('authorized');

    $app->group('/projects/{id}', function(RouteCollectorProxy $group) {
        $group->get('/detail', Detail::class)->setName('project_detail');

        $group->post('/hook', Register::class)->setName('post_hook');
        $group->post('/hook/{hookId}', Delete::class)->setName('delete_hook');

        $group->post('/file', Create::class);
        $group->post('/file/{fileId}', FileDelete::class);
    });

    $app->post('/hook/income', Income::class);
};
