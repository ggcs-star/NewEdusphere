<?php

namespace Piyush\PassportAuth\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Piyush\PassportAuth\Support\AuthResponse;
use Throwable;

class PassportAuthExceptionHandler
{
    /**
     * Handle authentication package exceptions.
     */
    public static function handle(Throwable $exception): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Unauthenticated
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof AuthenticationException) {
            return AuthResponse::unauthenticated();
        }

        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        if ($exception instanceof ValidationException) {
            return AuthResponse::validation(
                'Validation failed.',
                $exception->errors()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Default Error
        |--------------------------------------------------------------------------
        */

        return AuthResponse::error(
            'Something went wrong. Please try again.',
            [],
            500
        );
    }
}