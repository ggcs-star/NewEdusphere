<?php
namespace Piyush\PassportAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Piyush\PassportAuth\Support\AuthResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class LoginThrottle
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('passport-auth.security.login_throttle', true)) return $next($request);
        $email = strtolower((string) $request->input('email', ''));
        $key = 'passport-auth:login:' . sha1($email . '|' . $request->ip());
        $max = (int) config('passport-auth.security.max_attempts', 5);
        $decay = (int) config('passport-auth.security.lockout_minutes', 15) * 60;

        if (RateLimiter::tooManyAttempts($key, $max)) {
            return AuthResponse::error('Too many login attempts. Please try again later.', ['retry_after' => [RateLimiter::availableIn($key)]], 429);
        }

        try {
            $response = $next($request);
        } catch (Throwable $e) {
            RateLimiter::hit($key, $decay);
            throw $e;
        }

        if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) RateLimiter::hit($key, $decay);
        else RateLimiter::clear($key);
        return $response;
    }
}
