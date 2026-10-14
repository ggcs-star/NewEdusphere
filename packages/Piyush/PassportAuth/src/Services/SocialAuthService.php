<?php
namespace Piyush\PassportAuth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Piyush\PassportAuth\Contracts\TokenServiceInterface;

class SocialAuthService
{
    public function __construct(protected TokenServiceInterface $tokenService, protected ?SecurityService $security = null) {}

    public function redirect(string $provider)
    {
        $this->assertProvider($provider);
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback(string $provider): array
    {
        $this->assertProvider($provider);
        $social = Socialite::driver($provider)->stateless()->user();
        $model = config('passport-auth.user_model', \App\Models\User::class);
        $account = DB::table('passport_auth_social_accounts')->where('provider', $provider)->where('provider_id', (string) $social->getId())->first();
        $user = $account ? $model::find($account->user_id) : null;
        if (!$user && $social->getEmail()) $user = $model::where('email', $social->getEmail())->first();
        if (!$user) {
            $user = new $model();
            $this->fillUser($user, $social->getName(), $social->getEmail());
            $user->password = Hash::make(Str::random(40));
            $user->save();
        }
        DB::table('passport_auth_social_accounts')->updateOrInsert(
            ['provider' => $provider, 'provider_id' => (string) $social->getId()],
            ['user_id' => $user->getKey(), 'email' => $social->getEmail(), 'avatar' => $social->getAvatar(), 'updated_at' => now(), 'created_at' => now()]
        );
        $tokens = $this->tokenService->createPersonalToken($user);
        $this->security?->recordAudit($user->getKey(), 'social_login_success', ['metadata' => ['provider' => $provider]]);
        return ['user' => $user, 'tokens' => $tokens];
    }

    protected function assertProvider(string $provider): void
    {
        $allowed = (array) config('passport-auth.social_login.providers', ['google','facebook']);
        if (!in_array($provider, $allowed, true)) abort(404);
    }

    protected function fillUser(mixed $user, ?string $name, ?string $email): void
    {
        $first = config('passport-auth.registration.first_name_field', 'first_name');
        $last = config('passport-auth.registration.last_name_field', 'last_name');
        $parts = preg_split('/\s+/', trim((string) $name), 2);
        if ($this->hasAttribute($user, $first)) $user->{$first} = $parts[0] ?? null;
        if ($this->hasAttribute($user, $last)) $user->{$last} = $parts[1] ?? null;
        if ($this->hasAttribute($user, 'name')) $user->name = $name;
        if ($this->hasAttribute($user, 'email')) $user->email = $email;
    }

    protected function hasAttribute(mixed $user, string $field): bool
    {
        return array_key_exists($field, $user->getAttributes()) || in_array($field, $user->getFillable(), true);
    }
}
