<?php

use App\Models\Setting;
use App\Models\User;

if (! function_exists('format_price')) {
    /**
     * Formats an amount using the admin-configured System Currency and
     * Currency Position (Settings -> Payment) — e.g. "$10.00", "10.00$",
     * "₹ 399.00" — instead of every view hardcoding its own symbol.
     */
    function format_price(float|int $amount, int $decimals = 2): string
    {
        $code = Setting::get('system_currency', 'USD');
        $position = Setting::get('currency_position', 'left');
        $symbol = config('currencies')[$code]['symbol'] ?? '$';
        $number = number_format((float) $amount, $decimals);

        return match ($position) {
            'right' => "{$number}{$symbol}",
            'left-space' => "{$symbol} {$number}",
            'right-space' => "{$number} {$symbol}",
            default => "{$symbol}{$number}",
        };
    }
}

if (! function_exists('currency_symbol')) {
    /**
     * Just the admin-configured currency symbol (e.g. "$", "₹") — for the
     * rare spot that needs the bare symbol rather than a formatted amount.
     */
    function currency_symbol(): string
    {
        $code = Setting::get('system_currency', 'USD');

        return config('currencies')[$code]['symbol'] ?? '$';
    }
}

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
