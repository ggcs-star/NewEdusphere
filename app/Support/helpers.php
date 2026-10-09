<?php

use App\Models\User;

if (! function_exists('current_admin_user')) {
    /**
     * The authenticated user for the admin panel. Piyush's auth system
     * (WebAuthMiddleware / 'web.auth') doesn't use Laravel's session guard —
     * it stores the user directly in the session under the 'user' key once
     * login + device verification succeed. This is the one place that
     * knows that, so controllers/views never read session('user') raw.
     */
    function current_admin_user(): ?User
    {
        return session('user');
    }
}
