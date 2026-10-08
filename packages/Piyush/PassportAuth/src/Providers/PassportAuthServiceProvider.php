<?php

namespace Piyush\PassportAuth\Providers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\ServiceProvider;
use Piyush\PassportAuth\Contracts\AuthenticationServiceInterface;
use Piyush\PassportAuth\Contracts\TokenServiceInterface;
use Piyush\PassportAuth\Services\AuthenticationService;
use Piyush\PassportAuth\Services\TokenService;
use Piyush\PassportAuth\Support\AuthResponse;

class PassportAuthServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/passport-auth.php',
            'passport-auth'
        );

        $this->app->bind(
            AuthenticationServiceInterface::class,
            AuthenticationService::class
        );

        $this->app->bind(
            TokenServiceInterface::class,
            TokenService::class
        );
    }

    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/passport-auth.php'
                => config_path('passport-auth.php'),
        ], 'passport-auth-config');

        $this->loadRoutesFrom(
            __DIR__ . '/../../routes/api.php'
        );

        $this->loadMigrationsFrom(
            __DIR__ . '/../../database/migrations'
        );

        $this->registerExceptionHandling();
    }

    /**
     * Register package exception handling.
     */
    protected function registerExceptionHandling(): void
    {
        $this->app->make(
            \Illuminate\Contracts\Debug\ExceptionHandler::class
        )->renderable(function (
            AuthenticationException $exception,
            $request
        ) {
            if ($request->is(
                config('passport-auth.api_prefix', 'api/v1/auth') . '/*'
            )) {
                return AuthResponse::unauthenticated();
            }

            return null;
        });
    }
}