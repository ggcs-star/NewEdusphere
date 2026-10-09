<?php

namespace Piyush\PassportAuth\Services;

use Laravel\Passport\Token;
use Piyush\PassportAuth\Contracts\TokenServiceInterface;
use RuntimeException;

class TokenService implements TokenServiceInterface
{
    /**
     * Create authentication token for an authenticated user.
     *
     * Internal package authentication uses Passport Personal Access Token.
     * Password Grant remains available through Passport's /oauth/token endpoint
     * for external OAuth clients.
     */
    public function createToken(mixed $user, string $password): array
    {
        if (!$user) {
            throw new RuntimeException(
                'Unable to generate authentication token.'
            );
        }

        return $this->createPersonalToken($user);
    }

    /**
     * Create Passport personal access token.
     */
    public function createPersonalToken(mixed $user): array
    {
        if (!$user) {
            throw new RuntimeException(
                'Unable to generate authentication token.'
            );
        }

        $result = $user->createToken(
            config(
                'passport-auth.token_name',
                'passport-auth-token'
            )
        );

        return [
            'token_type' => 'Bearer',
            'access_token' => $result->accessToken,
        ];
    }

    /**
     * Refresh token.
     *
     * Refresh tokens are handled by Passport's OAuth endpoint:
     * POST /oauth/token
     *
     * grant_type=refresh_token
     */
    public function refreshToken(string $refreshToken): array
    {
        throw new RuntimeException(
            'Refresh tokens must be handled through the Passport OAuth token endpoint.'
        );
    }

    /**
     * Revoke current access token.
     */
    public function revokeCurrentToken(mixed $user): void
    {
        $token = $user->token();

        if ($token instanceof Token) {
            $token->revoke();
        }
    }

    /**
     * Revoke all access tokens.
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