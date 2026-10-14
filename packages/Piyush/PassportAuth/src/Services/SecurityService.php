<?php

namespace Piyush\PassportAuth\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SecurityService
{
    public function __construct(protected IpAddressService $ipService) {}

    public function recordLogin(mixed $user, bool $success, ?string $failureReason = null, array $context = []): void
    {
        if (!config('passport-auth.security.track_login_history', true)) return;

        DB::table('passport_auth_login_histories')->insert([
            'user_id' => $user?->getKey(),
            'email' => $context['email'] ?? $user?->email,
            'success' => $success ? 1 : 0,
            'failure_reason' => $failureReason,
            'ip_address' => $context['ip'] ?? $this->ipService->address(),
            'ip_chain' => config('passport-auth.security.store_ip_chain', true) ? json_encode($this->ipService->all()) : null,
            'user_agent' => $context['user_agent'] ?? request()->userAgent(),
            'device_id' => $context['device_id'] ?? null,
            'created_at' => now(),
        ]);
    }

    public function touchDevice(mixed $user, array $data): ?string
    {
        if (!config('passport-auth.security.track_devices', true)) return null;

        $deviceId = $data['device_id'] ?? (string) Str::uuid();
        $existing = DB::table('passport_auth_devices')
            ->where('user_id', $user->getKey())
            ->where('device_id', $deviceId)
            ->first();

        $payload = [
            'user_id' => $user->getKey(),
            'device_id' => $deviceId,
            'device_name' => $data['device_name'] ?? null,
            'platform' => $data['platform'] ?? null,
            'app_version' => $data['app_version'] ?? null,
            'ip_address' => $this->ipService->address(),
            'ip_chain' => config('passport-auth.security.store_ip_chain', true) ? json_encode($this->ipService->all()) : null,
            'user_agent' => request()->userAgent(),
            'last_active_at' => now(),
            'revoked_at' => null,
            'updated_at' => now(),
        ];

        if ($existing) {
            DB::table('passport_auth_devices')->where('id', $existing->id)->update($payload);
        } else {
            DB::table('passport_auth_devices')->insert($payload + ['created_at' => now()]);
        }

        return $deviceId;
    }

    public function devices(mixed $user): array
    {
        return DB::table('passport_auth_devices')
            ->where('user_id', $user->getKey())
            ->orderByDesc('last_active_at')
            ->get()
            ->map(fn ($device) => (array) $device)
            ->all();
    }

    public function revokeDevice(mixed $user, ?string $deviceId): void
    {
        $query = DB::table('passport_auth_devices')->where('user_id', $user->getKey());
        if ($deviceId) $query->where('device_id', $deviceId);
        $query->update(['revoked_at' => now(), 'updated_at' => now()]);
    }

    public function recordAudit(?int $userId, string $event, array $context = []): void
    {
        if (!config('passport-auth.security.audit_events', true)) return;

        DB::table('passport_auth_security_audits')->insert([
            'user_id' => $userId,
            'event' => $event,
            'ip_address' => $context['ip'] ?? $this->ipService->address(),
            'ip_chain' => config('passport-auth.security.store_ip_chain', true) ? json_encode($this->ipService->all()) : null,
            'user_agent' => $context['user_agent'] ?? request()->userAgent(),
            'device_id' => $context['device_id'] ?? null,
            'metadata' => !empty($context['metadata']) ? json_encode($context['metadata']) : null,
            'created_at' => now(),
        ]);
    }
}
