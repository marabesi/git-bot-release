<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab\Settings;

use App\UseCases\Gitlab\Settings\GetGitlabSettings;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Get
{
    private GetGitlabSettings $settingsUseCase;

    public function __construct(GetGitlabSettings $settingsUseCase)
    {
        $this->settingsUseCase = $settingsUseCase;
    }

    public function __invoke(Request $request, Response $response)
    {
        $setting = $this->settingsUseCase->list();

        $response->getBody()->write(sprintf('
                gitlab url: %s
                client id: %s
                secret: %s
                redirect url: %s
                state: %s',
                $setting->getGitlabUrl(),
                $setting->getClientId(),
                $setting->getSecret(),
                $setting->getRedirectUrl(),
                $setting->getState(),
            )
        );
        return $response;
    }
}