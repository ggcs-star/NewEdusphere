<?php

namespace Piyush\PassportAuth\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Piyush\PassportAuth\Contracts\AuthenticationServiceInterface;
use Piyush\PassportAuth\Http\Requests\LoginRequest;
use Piyush\PassportAuth\Http\Requests\RefreshTokenRequest;
use Piyush\PassportAuth\Http\Requests\RegisterRequest;
use Piyush\PassportAuth\Support\AuthResponse;

class AuthController extends Controller
{
    public function __construct(
        protected AuthenticationServiceInterface $authenticationService
    ) {
    }

    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authenticationService->register(
            $request->validated()
        );

        return AuthResponse::success(
            'Registration successful.',
            $result,
            201
        );
    }

    /**
     * Authenticate an existing user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authenticationService->login(
            $request->validated()
        );

        return AuthResponse::success(
            'Login successful.',
            $result
        );
    }

    /**
     * Refresh authentication tokens.
     */
    public function refresh(
        RefreshTokenRequest $request
    ): JsonResponse {
        $result = $this->authenticationService->refresh(
            $request->validated('refresh_token')
        );

        return AuthResponse::success(
            'Token refreshed successfully.',
            $result
        );
    }

    /**
     * Logout the authenticated user.
     */
    public function logout(): JsonResponse
    {
        $this->authenticationService->logout();

        return AuthResponse::success(
            'Logout successful.'
        );
    }

    /**
     * Get the authenticated user.
     */
    public function me(): JsonResponse
    {
        $user = $this->authenticationService->user();

        if (!$user) {
            return AuthResponse::unauthenticated();
        }

        return AuthResponse::success(
            'User retrieved successfully.',
            [
                'user' => $user,
            ]
        );
    }
}