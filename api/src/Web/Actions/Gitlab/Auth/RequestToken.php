<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab\Auth;

use App\Domain\Gitlab\Project\SettingsRepository;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Views\Twig;
use App\UseCases\Gitlab\Authentication\RequestToken as UseCase;

class RequestToken
{
    private SettingsRepository $settingsRepository;
    private Twig $twig;

    public function __construct(
        SettingsRepository $settings,
        Twig $twig
    ) {
        $this->settingsRepository = $settings;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response)
    {
        $settings = $this->settingsRepository->get();
        $token = new UseCase($settings);
        return $response->withAddedHeader('Location', $token->url());
    }
}
