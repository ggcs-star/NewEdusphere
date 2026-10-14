<?php
namespace Piyush\PassportAuth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Piyush\PassportAuth\Contracts\AuthenticationServiceInterface;
use Piyush\PassportAuth\Contracts\TokenServiceInterface;
use Piyush\PassportAuth\Notifications\EmailVerificationNotification;

class AuthenticationService implements AuthenticationServiceInterface
{
public function __construct(
    protected TokenServiceInterface $tokenService,
    protected LegacyPasswordService $legacyPasswordService,
    protected SecurityService $securityService
) {}

public function register(array $data): array
{
$model = config('passport-auth.user_model', \App\Models\User::class);

$user = new $model();

$table = config('passport-auth.users_table', 'users');

// Name
$first = config(
    'passport-auth.registration.first_name_field',
    'first_name'
);

$last = config(
    'passport-auth.registration.last_name_field',
    'last_name'
);

$name = $data['name'] ?? null;

// If name is not provided, create it from first + last name
if (!$name) {
    $name = trim(
        ($data[$first] ?? '') . ' ' . ($data[$last] ?? '')
    );
}

// Existing LMS users table requires name
if ($this->column($table, 'name')) {
    $user->name = $name ?: $data['email'];
}

// Email
$this->setIfColumn(
    $user,
    $table,
    'email',
    strtolower($data['email'])
);

// Password
$this->setIfColumn(
    $user,
    $table,
    'password',
    Hash::make($data['password'])
);

// First name
if (
    $this->column($table, $first) &&
    isset($data[$first])
) {
    $user->{$first} = $data[$first];
}

// Last name
if (
    $this->column($table, $last) &&
    isset($data[$last])
) {
    $user->{$last} = $data[$last];
}

// Status
if ($this->column($table, 'status')) {
    $user->status = config(
        'passport-auth.registration.default_status',
        1
    );
}

// Role
$role = config('passport-auth.registration.default_role_id');

if (
    $role !== null &&
    $this->column($table, 'role_id')
) {
    $user->role_id = $role;
}

// LMS date fields
if ($this->column($table, 'date_added')) {
    $user->date_added = time();
}

if ($this->column($table, 'last_modified')) {
    $user->last_modified = time();
}

// Save
$user->save();

// Email verification
if (
    config(
        'passport-auth.email_verification.enabled',
        false
    )
) {
    $this->sendEmailVerification($user);
}

// Passport tokens
return [
    'user' => $user,
    'tokens' => $this->tokenService->createToken(
        $user,
        $data['password']
    ),
];
}

public function login(array $credentials): array
{
    $model = config('passport-auth.user_model', \App\Models\User::class);
    $email = strtolower($credentials['email']);
    $password = $credentials['password'];
    $user = $model::where('email', $email)->first();
    if (!$user || !$this->legacyPasswordService->check($password, $user->password)) {
        $this->securityService->recordLogin($user, false, 'invalid_credentials', ['email' => $email]);
        $this->securityService->recordAudit($user?->getKey(), 'login_failed', ['metadata' => ['reason' => 'invalid_credentials', 'email' => $email]]);
        throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
    }
    if (array_key_exists('status', $user->getAttributes()) && (int) $user->status !== 1) {
        $this->securityService->recordLogin($user, false, 'account_inactive');
        $this->securityService->recordAudit($user?->getKey(), 'login_failed', ['metadata' => ['reason' => 'account_inactive']]);
        throw ValidationException::withMessages(['email' => ['This account is inactive.']]);
    }
    if ($this->legacyPasswordService->shouldUpgrade($user->password)) {
        $user->password = Hash::make($password);
        $user->save();
    }
    $deviceId = $this->securityService->touchDevice($user, $credentials);
    $this->securityService->recordLogin($user, true, null, ['device_id' => $deviceId]);
    $this->securityService->recordAudit($user->getKey(), 'login_success', ['device_id' => $deviceId]);
    return ['user' => $user, 'tokens' => $this->tokenService->createToken($user, $password)];
}

public function refresh(string $refreshToken): array
{
    return $this->tokenService->refreshToken($refreshToken);
}

public function logout(): void
{
    $user = $this->user();
    if ($user) { $this->tokenService->revokeCurrentToken($user); $this->securityService->recordAudit($user->getKey(), 'logout'); }
}

public function user(): mixed
{
    return auth()->guard('api')->user();
}

public function logoutAll(): void
{
    $user = $this->user();
    if ($user) { $this->tokenService->revokeAllTokens($user); $this->securityService->recordAudit($user->getKey(), 'logout_all'); }
}

public function changePassword(string $current, string $new): void
{
    $user = $this->user();
    if (!$user) throw new \Illuminate\Auth\AuthenticationException();
    if (!$this->legacyPasswordService->check($current, $user->password)) {
        throw ValidationException::withMessages(['current_password' => ['The current password is incorrect.']]);
    }
    $user->password = Hash::make($new);
    $user->save();
    $this->tokenService->revokeAllTokens($user);
    $this->securityService->recordAudit($user->getKey(), 'password_changed');
}

public function sendEmailVerification(mixed $user): void
{
    if (!method_exists($user, 'notify')) return;
    $token = Str::random(64);
    DB::table('passport_auth_email_verifications')->updateOrInsert(
        ['user_id' => $user->getKey()],
        ['token' => hash('sha256', $token), 'expires_at' => now()->addMinutes((int) config('passport-auth.email_verification.expiry', 60)), 'updated_at' => now(), 'created_at' => now()]
    );
    $user->notify(new EmailVerificationNotification($token));
}

public function verifyEmail(string $token): void
{
    $row = DB::table('passport_auth_email_verifications')->where('token', hash('sha256', $token))->first();
    if (!$row || now()->greaterThan($row->expires_at)) throw ValidationException::withMessages(['token' => ['The email verification token is invalid or expired.']]);
    $model = config('passport-auth.user_model', \App\Models\User::class);
    $user = $model::find($row->user_id);
    if (!$user) throw ValidationException::withMessages(['token' => ['The user could not be found.']]);
    $column = config('passport-auth.email_verification.verification_column', 'email_verified_at');
    if ($this->column(config('passport-auth.users_table', 'users'), $column)) { $user->{$column} = now(); $user->save(); }
    DB::table('passport_auth_email_verifications')->where('id', $row->id)->delete();
    $this->securityService->recordAudit($user->getKey(), 'email_verified');
}

protected function column(string $table, string $column): bool { return Schema::hasColumn($table, $column); }
protected function setIfColumn(mixed $user, string $table, string $column, mixed $value): void { if ($this->column($table, $column)) $user->{$column} = $value; }
}
