<?php
declare(strict_types=1);

use App\Web\Routes;
use Slim\App;
use App\Web\Actions\Gitlab\Projects\Detail;
use App\Web\Actions\Gitlab\Webhook\Register;
use App\Web\Actions\Gitlab\Webhook\Delete;
use App\Web\Actions\Gitlab\File\Create;
use App\Web\Actions\Gitlab\File\Delete as FileDelete;
use Slim\Routing\RouteCollectorProxy;

return function (App $app) {
    foreach (Routes::getAll() as $entry) {
        list($uri, $className, $routeName, $verb) = $entry;
        $app->{$verb}($uri, $className)->setName($routeName);
    }

    $app->group('/projects/{id}', function(RouteCollectorProxy $group) {
        $group->get('/detail', Detail::class)->setName('project_detail');

        $group->post('/hook', Register::class)->setName('post_hook');
        $group->post('/hook/{hookId}', Delete::class)->setName('delete_hook');

        $group->post('/file', Create::class);
        $group->post('/file/{fileId}', FileDelete::class);
    });
};
