<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab;

use App\Domain\Gitlab\Authentication\TokenNotFound;
use App\Domain\Gitlab\Entity\Settings;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use App\Domain\Gitlab\Authentication\GenerateToken;
use App\Domain\Gitlab\Authentication\TokenRepository;

class Welcome
{

    private Twig $twig;
    private Settings $settings;
    private GenerateToken $generateGitlabToken;
    private TokenRepository $gitlabRepository;

    public function __construct(
        Twig $twig,
        Settings $settings,
        GenerateToken $generateGitlabToken,
        TokenRepository $gitlabRepository
    ) {
        $this->twig = $twig;
        $this->settings = $settings;
        $this->generateGitlabToken = $generateGitlabToken;
        $this->gitlabRepository = $gitlabRepository;
    }

    public function __invoke(Request $request, Response $response) {
        try {
            $params = $request->getQueryParams();
            $error = $params['error'] ?? false;
            $code = $params['code'] ?? false;
            $state = $params['state'] ?? false;
            $token = $this->gitlabRepository->getToken();

            if (!empty($token)) {
                return $response->withAddedHeader('Location', '/authorized');
            }

            if ($error) {
                return $response->withAddedHeader('Location', '/unauthorized');
            }

            if ($code && $state) {
                $token = $this->generateGitlabToken->requestToken([
                    'client_id' => $this->settings->getClientId(),
                    'client_secret' => $this->settings->getSecret(),
                    'code' => $code,
                    'grant_type' => $this->settings->getGrantType(),
                    'redirect_uri' => $this->settings->getRedirectUrl(),
                ]);

                $this->gitlabRepository->storeToken($token);

                return $response->withAddedHeader('Location', '/authorized');
            }

        } catch (TokenNotFound $error) { }

        return $this->twig->render($response, 'templates/welcome.twig');
    }
}