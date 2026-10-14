<?php
namespace Piyush\PassportAuth\Services;

use Illuminate\Support\Facades\Hash;

class LegacyPasswordService
{
    public function check(string $plain, ?string $stored): bool
    {
        if (!$stored) return false;
        if (Hash::check($plain, $stored)) return true;
        if (!config('passport-auth.legacy_password.enabled', true)) return false;
        foreach ((array) config('passport-auth.legacy_password.algorithms', ['sha1','md5']) as $algorithm) {
            $candidate = match (strtolower($algorithm)) {
                'sha1' => sha1($plain),
                'md5' => md5($plain),
                default => null,
            };
            if ($candidate !== null && hash_equals($stored, $candidate)) return true;
        }
        return false;
    }

    public function shouldUpgrade(?string $stored): bool
    {
        return (bool) config('passport-auth.legacy_password.rehash_on_login', true)
            && $stored !== null
            && !str_starts_with($stored, '$2y$')
            && !str_starts_with($stored, '$argon2');
    }
}
