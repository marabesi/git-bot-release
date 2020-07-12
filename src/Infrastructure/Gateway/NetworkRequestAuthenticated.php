<?php

namespace Gitbot\Infrastructure;

use Exception;

class NetworkRequestAuthenticated
{
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function put(string $url, array $fieldsToPut): array
    {
        $content = json_encode($fieldsToPut);

        $ch = curl_init($url);
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
}