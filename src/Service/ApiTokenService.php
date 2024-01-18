<?php

namespace App\Service;

class ApiTokenService
{
    private $validToken;

    public function __construct(string $validToken)
    {
        $this->validToken = $validToken;
    }

    public function isTokenValid(string $token): bool
    {
        return $token === $this->validToken;
    }
}
