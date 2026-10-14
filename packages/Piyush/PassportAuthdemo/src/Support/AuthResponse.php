<?php

namespace Piyush\PassportAuth\Support;

use Illuminate\Http\JsonResponse;

class AuthResponse
{
    /**
     * Return a successful API response.
     *
     * @param string $message
     * @param mixed $data
     * @param int $status
     */
    public static function success(
        string $message,
        mixed $data = [],
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return an error API response.
     *
     * @param string $message
     * @param mixed $errors
     * @param int $status
     */
    public static function error(
        string $message,
        mixed $errors = [],
        int $status = 400
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    /**
     * Return a validation error response.
     *
     * @param string $message
     * @param mixed $errors
     */
    public static function validation(
        string $message = 'Validation failed.',
        mixed $errors = []
    ): JsonResponse {
        return self::error(
            $message,
            $errors,
            422
        );
    }

    /**
     * Return an unauthenticated response.
     */
    public static function unauthenticated(
        string $message = 'Unauthenticated.'
    ): JsonResponse {
        return self::error(
            $message,
            [],
            401
        );
    }

    /**
     * Return a forbidden response.
     */
    public static function forbidden(
        string $message = 'Forbidden.'
    ): JsonResponse {
        return self::error(
            $message,
            [],
            403
        );
    }
}