<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Consistent {success, message, data} JSON envelope for every Api\V1
 * controller, so frontend/mobile clients only ever parse one response
 * shape regardless of which endpoint they called.
 */
trait ApiResponses
{
    protected function ok(
        JsonResource|ResourceCollection|array|null $data = null,
        string $message = 'OK',
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function fail(
        string $message = 'Something went wrong.',
        int $status = 422,
        array $errors = []
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
