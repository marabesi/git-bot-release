<?php
declare(strict_types=1);

namespace App\Infrastructure\Gateway;

use App\Domain\Gitlab\Entity\Settings;
use Exception;

class NetworkRequestAuthenticated
{
    private string $token;
    private Settings $settings;

    public function __construct(string $token, Settings $settings)
    {
        $this->token = $token;
        $this->settings = $settings;
    }

    public function put(string $url, array $fieldsToPut): array
    {
        $content = json_encode($fieldsToPut);

        $ch = curl_init($this->settings->resolveGitlabUri($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch,  CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            "cache-control: no-cache",
            "content-type: application/json",
        ]);

        $result = curl_exec($ch);
        $error = curl_error($ch);

        if ($error) {
            throw new Exception($error);
        }

        return (array) json_decode($result, true);
    }

    public function post(string $url, array $fieldsToPost): array
    {
        $content = json_encode($fieldsToPost);

        $ch = curl_init($this->settings->resolveGitlabUri($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch,  CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            "cache-control: no-cache",
            "content-type: application/json",
        ]);

        $result = curl_exec($ch);
        $error = curl_error($ch);

        if ($error) {
            throw new Exception($error);
        }

        return (array) json_decode($result, true);
    }

    public function get(string $url, array $params = []): array
    {
        $query = http_build_query($params);

        $gitlab = $this->settings->resolveGitlabUri($url);
        $url = sprintf("$gitlab?%s", $query);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,  CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            "cache-control: no-cache",
            "content-type: application/json",
        ]);

        $result = curl_exec($ch);
        $error = curl_error($ch);

        if ($error) {
            throw new Exception($error);
        }

        return (array) json_decode($result, true);
    }

    public function delete($url): array
    {
        $ch = curl_init($this->settings->resolveGitlabUri($url));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch,  CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->token,
            "cache-control: no-cache",
            "content-type: application/json",
        ]);

        $result = curl_exec($ch);
        $error = curl_error($ch);

        if ($error) {
            throw new Exception($error);
        }

        return (array) json_decode($result, true);
    }
}