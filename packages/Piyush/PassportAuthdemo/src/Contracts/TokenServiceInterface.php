<?php

namespace Piyush\PassportAuth\Contracts;

interface TokenServiceInterface
{
    public function createToken(
        mixed $user,
        string $password
    ): array;

    public function refreshToken(
        string $refreshToken
    ): array;

    public function revokeCurrentToken(mixed $user): void;

    public function revokeAllTokens(mixed $user): void;
}