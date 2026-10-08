<?php

namespace Piyush\PassportAuth\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Piyush\PassportAuth\Contracts\AuthenticationServiceInterface;
use Piyush\PassportAuth\Contracts\TokenServiceInterface;

class AuthenticationService implements AuthenticationServiceInterface
{
    public function __construct(
        protected TokenServiceInterface $tokenService
    ) {
    }

    /**
     * Register a new user and issue Passport tokens.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function register(array $data): array
    {
        $userModel = config(
            'passport-auth.user_model',
            \App\Models\User::class
        );

        $user = new $userModel();

        $user->fill([
            'name' => $data['name'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->save();

        $tokens = $this->tokenService->createToken(
            $user,
            $data['password']
        );

        return [
            'user' => $user,
            'tokens' => $tokens,
        ];
    }

    /**
     * Authenticate user and issue Passport tokens.
     *
     * @param array<string, mixed> $credentials
     * @return array<string, mixed>
     */
    public function login(array $credentials): array
    {
        $email = $credentials['email'];
        $password = $credentials['password'];

        $userModel = config(
            'passport-auth.user_model',
            \App\Models\User::class
        );

        $user = $userModel::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [
                    'The provided credentials are incorrect.',
                ],
            ]);
        }

        $tokens = $this->tokenService->createToken(
            $user,
            $password
        );

        return [
            'user' => $user,
            'tokens' => $tokens,
        ];
    }

    /**
     * Refresh authentication tokens.
     *
     * @param string $refreshToken
     * @return array<string, mixed>
     */
    public function refresh(string $refreshToken): array
    {
        return $this->tokenService->refreshToken(
            $refreshToken
        );
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(): void
    {
        $user = Auth::guard('api')->user();

        if ($user) {
            $this->tokenService->revokeCurrentToken($user);
        }
    }

    /**
     * Get the authenticated user.
     */
    public function user(): mixed
    {
        return Auth::guard('api')->user();
    }
}