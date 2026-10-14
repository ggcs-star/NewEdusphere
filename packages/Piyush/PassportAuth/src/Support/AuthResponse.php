<?php

namespace Piyush\PassportAuth\Support;

use Illuminate\Http\JsonResponse;

class AuthResponse
{
    public static function success(string $message, mixed $data = [], int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function error(string $message, mixed $errors = [], int $status = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    public static function validation(string $message = 'Validation failed.', mixed $errors = []): JsonResponse
    {
        return self::error($message, $errors, 422);
    }

    public static function unauthenticated(string $message = 'Unauthenticated.'): JsonResponse
    {
        return self::error($message, [], 401);
    }

    public static function forbidden(string $message = 'Forbidden.'): JsonResponse
    {
        return self::error($message, [], 403);
    }
}
