<?php

namespace Tests\Feature\Stubs;

use App\Domain\Gitlab\Authentication\CouldNotEraseTokenException;
use App\Domain\Gitlab\Authentication\CouldNotStoreTokenException;
use App\Domain\Gitlab\Authentication\TokenNotFound;
use App\Domain\Gitlab\Authentication\TokenRepository;

class WithoutToken implements TokenRepository
{

    /**
     * @param $token string
     * @return bool
     * @throws CouldNotStoreTokenException
     */
    public function storeToken(string $token): bool
    {
        return false;
    }

    /**
     * @throws TokenNotFound
     */
    public function getToken(): string
    {
        return '';
    }

    /**
     * @throws CouldNotEraseTokenException
     */
    public function deleteToken(): bool
    {
        return false;
    }
}