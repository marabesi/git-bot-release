<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Logger;

return function (ContainerBuilder $containerBuilder) {
    $debug = require __DIR__ . '/debug.php';
    $containerBuilder->addDefinitions([
        'settings' => [
            'displayErrorDetails' => $debug['debug'],
            'debug' => $debug['debug'],
            'logger' => [
                'name' => 'git-bot',
                'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
        ],
    ]);
};
