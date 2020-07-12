<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use App\Domain\Gitlab\Settings;
use Slim\Views\TwigExtension;
use Slim\Views\Twig;
use App\Infrastructure\Gateway\NetworkRequest;
use App\Domain\Gitlab\GenerateToken;
use App\Domain\Gitlab\GitlabRepository;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Cache\Adapter\Filesystem\FilesystemCachePool;
use App\Infrastructure\Persistence\Gitlab\FilesystemRepository;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        LoggerInterface::class => function (ContainerInterface $c) {
            $settings = $c->get('settings');

            $loggerSettings = $settings['logger'];
            $logger = new Logger($loggerSettings['name']);

            $processor = new UidProcessor();
            $logger->pushProcessor($processor);

            $handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
            $logger->pushHandler($handler);

            return $logger;
        },
    ]);

    $containerBuilder->addDefinitions([
        Settings::class => function() {
            return new Settings(
                $_ENV['GITLAB_URL'],
                $_ENV['CLIENT_ID'],
                $_ENV['SECRET'],
                $_ENV['REDIRECT_URL'],
                $_ENV['STATE'],
            );
        }
    ]);

    $containerBuilder->addDefinitions([
         Twig::class => function () {
            $view = Twig::create('view', [
                'cache' => 'var/cache',
                'debug' => true,
            ]);

            $view->addExtension(new TwigExtension());
        return $view;
    }]);

    $containerBuilder->addDefinitions([
        NetworkRequest::class => function() {
            return new NetworkRequest();
        }
    ]);

    $containerBuilder->addDefinitions([
        GenerateToken::class => function(ContainerInterface $c) {
            return new GenerateToken(
                $c->get(NetworkRequest::class),
                $c->get(Settings::class)
            );
        }
    ]);

    $containerBuilder->addDefinitions([
        FilesystemCachePool::class => function() {
            $filesystemAdapter = new Local('var/storage');
            $filesystem = new Filesystem($filesystemAdapter);
            return new FilesystemCachePool($filesystem);
        }
    ]);

    $containerBuilder->addDefinitions([
        GitlabRepository::class => function(ContainerInterface $c) {
            return new FilesystemRepository(
                $c->get(FilesystemCachePool::class)
            );
        }
    ]);
};
