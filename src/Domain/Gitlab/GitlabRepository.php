<?php

namespace App\Domain\Gitlab;

interface GitlabRepository
{

    public function storeToken(string $token): bool;

    public function getToken(): string;
}