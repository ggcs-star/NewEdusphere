<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceIdentificationMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $deviceId =
            $request->header('X-Device-ID')
            ?? $request->input('device_id')
            ?? $request->session()->get('device_id');

        if (!$deviceId) {
            return response()->json([
                'success' => false,
                'message' => 'Device ID is required.',
            ], 422);
        }

        $request->attributes->set(
            'device_id',
            $deviceId
        );

        return $next($request);
    }
}