<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [

        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // Sanctum removed
            \Illuminate\Routing\Middleware\ThrottleRequests::class . ':api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [

        /*
        |--------------------------------------------------------------------------
        | Authentication
        |--------------------------------------------------------------------------
        */

        'auth' => \App\Http\Middleware\Authenticate::class,

        'web.auth' => \App\Http\Middleware\WebAuthMiddleware::class,

        'auth.basic' =>
            \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,

        'auth.session' =>
            \Illuminate\Session\Middleware\AuthenticateSession::class,

        /*
        |--------------------------------------------------------------------------
        | Authorization / Cache
        |--------------------------------------------------------------------------
        */

        'cache.headers' =>
            \Illuminate\Http\Middleware\SetCacheHeaders::class,

        'can' =>
            \Illuminate\Auth\Middleware\Authorize::class,

        /*
        |--------------------------------------------------------------------------
        | Guest
        |--------------------------------------------------------------------------
        */

        'guest' =>
            \App\Http\Middleware\RedirectIfAuthenticated::class,

        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        'password.confirm' =>
            \Illuminate\Auth\Middleware\RequirePassword::class,

        /*
        |--------------------------------------------------------------------------
        | Precognitive
        |--------------------------------------------------------------------------
        */

        'precognitive' =>
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,

        /*
        |--------------------------------------------------------------------------
        | Signed
        |--------------------------------------------------------------------------
        */

        'signed' =>
            \App\Http\Middleware\ValidateSignature::class,

        /*
        |--------------------------------------------------------------------------
        | Throttle
        |--------------------------------------------------------------------------
        */

        'throttle' =>
            \Illuminate\Routing\Middleware\ThrottleRequests::class,

        /*
        |--------------------------------------------------------------------------
        | Email Verification
        |--------------------------------------------------------------------------
        */

        'verified' =>
            \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        /*
        |--------------------------------------------------------------------------
        | Device Identification
        |--------------------------------------------------------------------------
        */

        'device.identification' =>
            \App\Http\Middleware\DeviceIdentificationMiddleware::class,
    ];
}