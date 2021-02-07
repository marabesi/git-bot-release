<?php

declare(strict_types=1);

use App\Web\ResponseEmitter\ResponseEmitter;

require __DIR__ . '/../vendor/autoload.php';

$bootstrap = require __DIR__ .  '/../app/bootstrap.php';
$container = $bootstrap['container'];

// Run App & Emit Response
$response = $app->handle($bootstrap['request']);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);
