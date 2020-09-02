<?php
declare(strict_types=1);

namespace App\Application\Actions\Gitlab\Auth;

use App\Domain\Gitlab\Authentication\GenerateToken;
use App\Domain\Gitlab\Entity\Settings;
use Slim\Psr7\Response;
use Slim\Psr7\Request;
use Slim\Views\Twig;

class RequestToken
{
    private Settings $settings;
    private Twig $twig;

    public function __construct(
        Settings $settings,
        Twig $twig
    ) {
        $this->settings = $settings;
        $this->twig = $twig;
    }

    public function __invoke(Request $request, Response $response)
    {
        $url = $this->settings->resolveGitlabUri(GenerateToken::OAUTH_AUTHORIZE);
        $generateCodeAndState = sprintf(
            '%s?client_id=%s&redirect_uri=%s&response_type=code&state=%s&scope=%s',
            $url,
            $this->settings->getClientId(),
            $this->settings->getRedirectUrl(),
            $this->settings->getState(),
            'read_repository+write_repository+api'
        );

        return $response->withAddedHeader('Location', $generateCodeAndState);
    }
}
