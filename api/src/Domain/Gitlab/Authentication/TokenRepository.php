<?php
declare(strict_types=1);

namespace App\Domain\Gitlab\Authentication;

interface TokenRepository
{

    /**
     * @param $token string
     * @return bool
     * @throws CouldNotStoreTokenException
     */
    public function storeToken(string $token): bool;

    /**
     * @throws TokenNotFound
     */
    public function getToken(): string;

    /**
     * @throws CouldNotEraseTokenException
     */
    public function deleteToken(): bool;
}