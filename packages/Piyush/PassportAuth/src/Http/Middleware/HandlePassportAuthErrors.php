<?php

namespace Piyush\PassportAuth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Piyush\PassportAuth\Exceptions\PassportAuthExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HandlePassportAuthErrors
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (Throwable $exception) {
            return PassportAuthExceptionHandler::handle($exception);
        }
    }
}
