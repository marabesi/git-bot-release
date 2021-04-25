<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab;

use App\Domain\Gitlab\Project\SettingsRepository;
use Exception;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use App\Domain\Gitlab\Authentication\GenerateToken;
use App\Domain\Gitlab\Authentication\TokenRepository;

class Welcome
{

    private Twig $twig;
    private SettingsRepository $settingsRepository;
    private GenerateToken $generateGitlabToken;
    private TokenRepository $tokenRepository;

    public function __construct(
        Twig $twig,
        SettingsRepository $settingsRepository,
        GenerateToken $generateGitlabToken,
        TokenRepository $tokenRepository
    ) {
        $this->twig = $twig;
        $this->settingsRepository = $settingsRepository;
        $this->generateGitlabToken = $generateGitlabToken;
        $this->tokenRepository = $tokenRepository;
    }

    public function __invoke(Request $request, Response $response) {
        $params = $request->getQueryParams();
        $error = $params['error'] ?? false;
        $code = $params['code'] ?? false;
        $state = $params['state'] ?? false;
        $token = '';

        try {
            $token = $this->tokenRepository->getToken();
        } catch (Exception $tokenNotFound) { }

        if (!empty($token)) {
            return $response->withHeader('Location', '/authorized');
        }

        if ($error) {
            return $response->withHeader('Location', '/unauthorized');
        }

        if ($code && $state) {
            $settings = $this->settingsRepository->get();
            $token = $this->generateGitlabToken->requestToken([
                'client_id' => $settings->getClientId(),
                'client_secret' => $settings->getSecret(),
                'code' => $code,
                'grant_type' => $settings->getGrantType(),
                'redirect_uri' => $settings->getRedirectUrl(),
            ]);

            $this->tokenRepository->storeToken($token);

            return $response->withHeader('Location', '/authorized');
        }

        return $this->twig->render($response, 'templates/welcome.twig');
    }
}