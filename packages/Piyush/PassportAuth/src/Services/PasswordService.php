<?php
namespace Piyush\PassportAuth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Piyush\PassportAuth\Notifications\PasswordResetNotification;

class PasswordService
{
    public function __construct(protected ?SecurityService $security = null) {}
    public function forgot(string $email): void
    {
        $model = config('passport-auth.user_model', \App\Models\User::class);
        $user = $model::where('email', $email)->first();
        if (!$user) return;
        $token = Str::random(64);
        DB::table('passport_auth_password_resets')->where('email', $email)->delete();
        DB::table('passport_auth_password_resets')->insert([
            'email' => $email,
            'token' => hash('sha256', $token),
            'created_at' => now(),
        ]);
        if (method_exists($user, 'notify')) $user->notify(new PasswordResetNotification($token));
        $this->security?->recordAudit($user->getKey(), 'password_reset_requested');
    }

    public function reset(string $email, string $token, string $password): void
    {
        $row = DB::table('passport_auth_password_resets')->where('email', $email)->first();
        $expiry = (int) config('passport-auth.password_reset.expiry', 60);
        if (!$row || now()->diffInMinutes($row->created_at) > $expiry || !hash_equals($row->token, hash('sha256', $token))) {
            throw ValidationException::withMessages(['token' => ['The password reset token is invalid or expired.']]);
        }
        $model = config('passport-auth.user_model', \App\Models\User::class);
        $user = $model::where('email', $email)->first();
        if (!$user) throw ValidationException::withMessages(['email' => ['User not found.']]);
        $user->password = Hash::make($password);
        $user->save();
        $user->tokens()->where('revoked', false)->update(['revoked' => true]);
        DB::table('passport_auth_password_resets')->where('email', $email)->delete();
        $this->security?->recordAudit($user->getKey(), 'password_reset_completed');
    }

    public function change(mixed $user, string $current, string $new): void
    {
        $legacy = new LegacyPasswordService();
        if (!$legacy->check($current, $user->password)) {
            throw ValidationException::withMessages(['current_password' => ['The current password is incorrect.']]);
        }
        $user->password = Hash::make($new);
        $user->save();
        $user->tokens()->where('revoked', false)->update(['revoked' => true]);
    }
}
