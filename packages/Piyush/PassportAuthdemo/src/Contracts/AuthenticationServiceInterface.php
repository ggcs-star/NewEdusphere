<?php

namespace Piyush\PassportAuth\Contracts;

interface AuthenticationServiceInterface
{
    /**
     * Register a new user.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function register(array $data): array;

    /**
     * Authenticate an existing user.
     *
     * @param array<string, mixed> $credentials
     * @return array<string, mixed>
     */
    public function login(array $credentials): array;

    /**
     * Refresh the authentication tokens.
     *
     * @param string $refreshToken
     * @return array<string, mixed>
     */
    public function refresh(string $refreshToken): array;

    /**
     * Logout the currently authenticated user.
     *
     * @return void
     */
    public function logout(): void;

    /**
     * Get the currently authenticated user.
     *
     * @return mixed
     */
    public function user(): mixed;
}