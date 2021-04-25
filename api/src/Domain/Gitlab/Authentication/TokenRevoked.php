<?php

namespace App\Domain\Gitlab\Authentication;

use Exception;
use Throwable;

class TokenRevoked extends Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct(sprintf('Token has been revoked by the user %s', $message), $code, $previous);
    }
}