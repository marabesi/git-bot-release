<?php
declare(strict_types=1);

use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use App\Domain\Gitlab\Entity\Settings;
use Slim\Views\TwigExtension;
use Slim\Views\Twig;
use App\Infrastructure\Gateway\NetworkRequest;
use App\Domain\Gitlab\Authentication\GenerateToken;
use App\Domain\Gitlab\Authentication\TokenRepository;
use App\Infrastructure\Persistence\Gitlab\TokenFilesystemRepository;
use App\Domain\Gitlab\Project\ProjectsRepository;
use App\Domain\Gitlab\Webhook\WebhookRepository;
use App\Infrastructure\Gateway\Gitlab\WebHookApiRepository;
use App\Infrastructure\Gateway\NetworkRequestAuthenticated;
use App\Domain\Gitlab\Entity\Webhook;
use App\Domain\Gitlab\File\FileRepository;
use App\Infrastructure\Gateway\Gitlab\FileApiRepository;
use App\Domain\Gitlab\Pipeline\PipelineRepository;
use App\Infrastructure\Gateway\Gitlab\PipelineApiRepository;
use App\Infrastructure\Gateway\Gitlab\ProjectsApiRepository;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use App\Domain\Gitlab\Tag\TagRepository;
use App\Infrastructure\Gateway\Gitlab\TagApiRepository;
use App\Infrastructure\Gateway\Gitlab\VersionApiRepository;
use App\Domain\Gitlab\Version\VersionRepository;
use App\Domain\Gitlab\Project\FilesToReleaseRepository;
use App\Infrastructure\Persistence\FilesystemDatabase;
use Filebase\Database;

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
        NetworkRequest::class => function() {
            return new NetworkRequest();
        }
    ]);

    $containerBuilder->addDefinitions([
         Twig::class => function () {
            $view = Twig::create('view', [
                'cache' => 'var/cache',
                'debug' => $_ENV['DEBUG'] === 'true',
            ]);

            $view->addExtension(new TwigExtension());
        return $view;
    }]);

    $containerBuilder->addDefinitions([
        GenerateToken::class => function(ContainerInterface $c) {
            return new GenerateToken(
                $c->get(NetworkRequest::class),
                $c->get(Settings::class)
            );
        }
    ]);

    $containerBuilder->addDefinitions([
        TokenRepository::class => function(ContainerInterface $c) {
            return new TokenFilesystemRepository(
                new FilesystemAdapter(
                    'cache',
                    0,
                    __DIR__ . '/../var/storage'
                ),
            );
        }
    ]);

    $containerBuilder->addDefinitions([
        NetworkRequestAuthenticated::class => function(ContainerInterface $c) {
            return new NetworkRequestAuthenticated(
                $c->get(TokenRepository::class)->getToken(),
                $c->get(Settings::class)
            );
        }
    ]);

    $containerBuilder->addDefinitions([
        ProjectsRepository::class => function(ContainerInterface $c) {
            return new ProjectsApiRepository(
                $c->get(NetworkRequestAuthenticated::class)
            );
        }
    ]);

    $containerBuilder->addDefinitions([
        WebhookRepository::class => function(ContainerInterface $c) {
            return new WebHookApiRepository(
                $c->get(NetworkRequestAuthenticated::class)
            );
        }
    ]);

    $containerBuilder->addDefinitions([
        FileRepository::class => function(ContainerInterface $c) {
            return new FileApiRepository(
                $c->get(NetworkRequestAuthenticated::class),
                $c->get(FileApiRepository::class)
            );
        }
    ]);

    $containerBuilder->addDefinitions([
        Webhook::class => function() {
            return new Webhook(
                $_ENV['WEBHOOK_INCOME_URL'],
                $_ENV['WEBHOOK_TOKEN'],
                (bool) $_ENV['WEBHOOK_PUSH'],
                (bool) $_ENV['WEBHOOK_ENABLE_SSL_VERIFICATION']
            );
        }
    ]);

    $containerBuilder->addDefinitions([
        PipelineRepository::class => function(ContainerInterface $c) {
            return new PipelineApiRepository(
                $c->get(NetworkRequestAuthenticated::class)
            );
        }
    ]);

    $containerBuilder->addDefinitions([
       TagRepository::class => function(ContainerInterface $c) {
            return new TagApiRepository(
                $c->get(NetworkRequestAuthenticated::class)
            );
       }
    ]);

    $containerBuilder->addDefinitions([
       VersionRepository::class => function(ContainerInterface $c) {
            return new VersionApiRepository(
                $c->get(NetworkRequestAuthenticated::class)
            );
       }
    ]);

    $containerBuilder->addDefinitions([
       FilesToReleaseRepository::class => function() {
            return new FilesystemDatabase(
                new Database([
                    'dir' => __DIR__ . '/../var/storage/database'
                ])
            );
       }
    ]);
};
