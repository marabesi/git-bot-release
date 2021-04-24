<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab\Settings;

use App\UseCases\Gitlab\Settings\GetGitlabSettings;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class Get
{
    private GetGitlabSettings $settingsUseCase;
    private Twig $twig;

    public function __construct(GetGitlabSettings $settingsUseCase, Twig $twig)
    {
        $this->settingsUseCase = $settingsUseCase;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response)
    {
        return $this->twig->render($response,  'templates/settings/index.twig', [
            'setting' => $this->settingsUseCase->list(),
        ]);
    }
}