<?php

namespace Piyush\PassportAuth\Services;

use Illuminate\Support\Facades\Http;
use Laravel\Passport\Token;
use Piyush\PassportAuth\Contracts\TokenServiceInterface;

class TokenService implements TokenServiceInterface
{
    /**
     * Create Passport access and refresh tokens.
     */
    public function createToken(
        mixed $user,
        string $password
    ): array {
        $response = Http::asForm()->post(
            url('/oauth/token'),
            [
                'grant_type' => 'password',

                'client_id' => config(
                    'passport-auth.password_grant.client_id'
                ),

                'client_secret' => config(
                    'passport-auth.password_grant.client_secret'
                ),

                'username' => $user->email,

                'password' => $password,

                'scope' => config(
                    'passport-auth.password_grant.scope',
                    ''
                ),
            ]
        );

        if ($response->failed()) {
            throw new \RuntimeException(
                $response->json('message')
                    ?? 'Unable to generate authentication token.'
            );
        }

        return $response->json();
    }

    /**
     * Refresh Passport access token.
     */
    public function refreshToken(
        string $refreshToken
    ): array {
        $response = Http::asForm()->post(
            url('/oauth/token'),
            [
                'grant_type' => 'refresh_token',

                'refresh_token' => $refreshToken,

                'client_id' => config(
                    'passport-auth.password_grant.client_id'
                ),

                'client_secret' => config(
                    'passport-auth.password_grant.client_secret'
                ),

                'scope' => config(
                    'passport-auth.password_grant.scope',
                    ''
                ),
            ]
        );

        if ($response->failed()) {
            throw new \RuntimeException(
                $response->json('message')
                    ?? 'Unable to refresh authentication token.'
            );
        }

        return $response->json();
    }

    /**
     * Revoke the current access token.
     */
    public function revokeCurrentToken(mixed $user): void
    {
        $token = $user->token();

        if ($token instanceof Token) {
            $token->revoke();
        }
    }

    /**
     * Revoke all tokens belonging to the user.
     */
    public function revokeAllTokens(mixed $user): void
    {
        $user->tokens()
            ->where('revoked', false)
            ->update([
                'revoked' => true,
            ]);
    }
}