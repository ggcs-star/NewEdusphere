<?php
namespace Piyush\PassportAuth\Contracts;
interface AuthenticationServiceInterface
{
    public function register(array $data): array;
    public function login(array $credentials): array;
    public function refresh(string $refreshToken): array;
    public function logout(): void;
    public function logoutAll(): void;
    public function changePassword(string $current, string $new): void;
    public function sendEmailVerification(mixed $user): void;
    public function verifyEmail(string $token): void;
    public function user(): mixed;
}
