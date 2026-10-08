<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebAuthMiddleware
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $accessToken = $request->session()->get('user_token');
        $user = $request->session()->get('user');

        if (empty($accessToken) || empty($user)) {
            $request->session()->forget([
                'user_token',
                'user',
                'device_id',
            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Please login to continue.');
        }

        return $next($request);
    }
}