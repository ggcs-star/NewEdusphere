<?php

namespace Piyush\PassportAuth\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Piyush\PassportAuth\Support\AuthResponse;
use Throwable;

class PassportAuthExceptionHandler
{
    public static function handle(Throwable $exception): JsonResponse
    {
        if ($exception instanceof AuthenticationException) {
            return AuthResponse::unauthenticated();
        }

        if ($exception instanceof ValidationException) {
            return AuthResponse::validation('Validation failed.', $exception->errors());
        }

        $status = (int) ($exception->getCode() >= 400 && $exception->getCode() < 600 ? $exception->getCode() : 500);

        return AuthResponse::error(
            $status === 500 ? 'Something went wrong. Please try again.' : $exception->getMessage(),
            [],
            $status
        );
    }
}
