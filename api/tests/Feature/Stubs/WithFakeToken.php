<?php

namespace Tests\Feature\Stubs;

use App\Domain\Gitlab\Authentication\CouldNotEraseTokenException;
use App\Domain\Gitlab\Authentication\CouldNotStoreTokenException;
use App\Domain\Gitlab\Authentication\TokenNotFound;
use App\Domain\Gitlab\Authentication\TokenRepository;

class WithFakeToken implements TokenRepository
{

    /**
     * @param $token string
     * @return bool
     * @throws CouldNotStoreTokenException
     */
    public function storeToken(string $token): bool
    {
        return true;
    }

    /**
     * @throws TokenNotFound
     */
    public function getToken(): string
    {
        return 'fake_token';
    }

    /**
     * @throws CouldNotEraseTokenException
     */
    public function deleteToken(): bool
    {
        return true;
    }
}
