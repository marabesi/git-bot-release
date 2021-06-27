<?php
declare(strict_types=1);

use App\Domain\Gitlab\Project\SettingsRepository;
use App\Infrastructure\Persistence\Gitlab\SettingsFilesystemRepository;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
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
use App\Domain\Gitlab\Authentication\TokenNotFound;
use Filebase\Database;
use GuzzleHttp\Client;

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
        SettingsRepository::class => fn(ContainerInterface $c) => new SettingsFilesystemRepository(
            new FilesystemAdapter(
                sprintf('settings_fs_%s', $c->get('settings')['environment'])
            )
        )
    ]);

    $containerBuilder->addDefinitions([
        NetworkRequest::class => function() {
            return new NetworkRequest(new Client());
        }
    ]);

    $containerBuilder->addDefinitions([
        Twig::class => function (ContainerInterface $c) {
            $debug = $c->get('settings')['debug'];
            $config = [
                'debug' => $debug,
            ];

            if ($debug === false) {
                $config['cache'] = __DIR__ . '/../var/cache';
            }

            $view = Twig::create(__DIR__ . '/../view', $config);
            $view->addExtension(new TwigExtension());

            return $view;
        }]);

    $containerBuilder->addDefinitions([
        GenerateToken::class => function(ContainerInterface $c) {
            return new GenerateToken(
                $c->get(NetworkRequest::class),
                $c->get(SettingsRepository::class)
            );
        }
    ]);

    $containerBuilder->addDefinitions([
        TokenRepository::class => function(ContainerInterface $c) {
            return new TokenFilesystemRepository(
                new FilesystemAdapter(
                    sprintf('token_%s', $c->get('settings')['environment']),
                    0,
                ),
            );
        }
    ]);

    $containerBuilder->addDefinitions([
        NetworkRequestAuthenticated::class => function(ContainerInterface $c) {
            try {
                $token = $c->get(TokenRepository::class)->getToken();
            } catch (TokenNotFound $error) {
                $token = '';
            }

            return new NetworkRequestAuthenticated(
                $token,
                $c->get(SettingsRepository::class)->get(),
                new Client()
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
            );
        }
    ]);

    $containerBuilder->addDefinitions([
        Webhook::class => fn(ContainerInterface $c) => $c->get(SettingsRepository::class)->getWebhook()
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
