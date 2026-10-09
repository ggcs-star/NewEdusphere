<?php
namespace Piyush\PassportAuth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Piyush\PassportAuth\Notifications\OtpNotification;

class OtpService
{
    public function __construct(protected ?SecurityService $security = null) {}
    public function send(string $email): void
    {
        $model = config('passport-auth.user_model', \App\Models\User::class);
        $user = $model::where('email', $email)->first();
        if (!$user) return;
        $existing = DB::table('passport_auth_otps')->where('email', $email)->whereNull('used_at')->latest('id')->first();
        $cooldown = (int) config('passport-auth.otp.resend_cooldown', 60);
        if ($existing && now()->diffInSeconds($existing->created_at) < $cooldown) {
            throw ValidationException::withMessages(['otp' => ['Please wait before requesting another OTP.']]);
        }
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        DB::table('passport_auth_otps')->where('email', $email)->whereNull('used_at')->update(['used_at' => now()]);
        DB::table('passport_auth_otps')->insert([
            'email' => $email,
            'otp_hash' => hash('sha256', $otp),
            'attempts' => 0,
            'expires_at' => now()->addMinutes((int) config('passport-auth.otp.expiry', 10)),
            'created_at' => now(),
        ]);
        if (method_exists($user, 'notify')) $user->notify(new OtpNotification($otp));
        $this->security?->recordAudit($user->getKey(), 'otp_sent');
    }

    public function verify(string $email, string $otp): void
    {
        $model = config('passport-auth.user_model', \App\Models\User::class);
        $row = DB::table('passport_auth_otps')->where('email', $email)->whereNull('used_at')->latest('id')->first();
        if (!$row || now()->greaterThan($row->expires_at)) throw ValidationException::withMessages(['otp' => ['The OTP is invalid or expired.']]);
        if ($row->attempts >= (int) config('passport-auth.otp.max_attempts', 5)) throw ValidationException::withMessages(['otp' => ['Maximum OTP attempts exceeded.']]);
        if (!hash_equals($row->otp_hash, hash('sha256', $otp))) {
            DB::table('passport_auth_otps')->where('id', $row->id)->increment('attempts');
            throw ValidationException::withMessages(['otp' => ['The OTP is incorrect.']]);
        }
        DB::table('passport_auth_otps')->where('id', $row->id)->update(['used_at' => now()]);
        $user = $model::where('email', $email)->first();
        $this->security?->recordAudit($user?->getKey(), 'otp_verified');
    }
}
