<?php

namespace App\Web\Actions\Gitlab\Settings;

use App\Domain\Gitlab\Entity\Settings;
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
        ));
        return $response->withHeader('Location', '/settings');
    }
}