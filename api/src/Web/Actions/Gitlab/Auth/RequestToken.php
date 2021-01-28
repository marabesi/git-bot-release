<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab\Auth;

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
        $token = new \App\UseCases\Gitlab\Authentication\RequestToken($this->settings);
        return $response->withAddedHeader('Location', $token->url());
    }
}
