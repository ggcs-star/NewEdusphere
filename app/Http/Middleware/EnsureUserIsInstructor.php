<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsInstructor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = current_admin_user();

        if (! $user || (! $user->isInstructor() && ! $user->isAdmin())) {
            abort(403, 'This area is restricted to instructors.');
        }

        // Admins can always preview the instructor portal (e.g. to re-enable
        // the setting); real instructors lose access while it's off —
        // matching Settings -> Instructor -> "Allow users to become
        // instructors and sell courses".
        if (! $user->isAdmin() && Setting::get('allow_instructor', '1') !== '1') {
            return redirect()
                ->route('home')
                ->with('error', 'Instructor features are currently disabled.');
        }

        return $next($request);
    }
}
