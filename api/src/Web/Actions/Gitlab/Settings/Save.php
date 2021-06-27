<?php
declare(strict_types=1);

namespace App\Web\Actions\Gitlab\Settings;

use App\Domain\Gitlab\Entity\Settings;
use App\Domain\Gitlab\Entity\Webhook;
use App\UseCases\Gitlab\Settings\SaveGitlabSettings;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class Save
{

    private SaveGitlabSettings $saveSettings;

    public function __construct(SaveGitlabSettings $saveSettings)
    {
        $this->saveSettings = $saveSettings;
    }

    public function __invoke(Request $request, Response $response)
    {
        $body = $request->getParsedBody();

        $this->saveSettings->save(new Settings(
            $body['gitlab_url'],
            $body['client_id'],
            $body['secret'],
            $body['redirect_url'],
            $body['state'],
        ),
        new Webhook(
            $body['webhook_url'],
            $body['webhook_token'],
            $body['webhook_push_events'],
            $body['webhook_enable_ssl_verification'],
        ));
        return $response->withHeader('Location', '/');
    }
}