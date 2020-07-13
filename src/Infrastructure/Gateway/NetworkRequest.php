<?php
declare(strict_types=1);

namespace App\Infrastructure\Gateway;

use Exception;

class NetworkRequest
{

    public function post(string $url, array $fieldsToPost): array
    {
        $content = http_build_query($fieldsToPost);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

        $result = curl_exec($ch);
        $error = curl_error($ch);

        if ($error) {
            throw new Exception($error);
        }

        return (array) json_decode($result, true);
    }
}