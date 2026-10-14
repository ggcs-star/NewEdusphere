<?php

return [
    'user_model' => env('PASSPORT_AUTH_USER_MODEL', App\Models\User::class),
    'users_table' => env('PASSPORT_AUTH_USERS_TABLE', 'users'),
    'api_prefix' => env('PASSPORT_AUTH_API_PREFIX', 'api/v1/auth'),
    'token_name' => env('PASSPORT_AUTH_TOKEN_NAME', 'passport-auth-token'),

    'password_grant' => [
        'client_id' => env('PASSPORT_AUTH_PASSWORD_CLIENT_ID'),
        'client_secret' => env('PASSPORT_AUTH_PASSWORD_CLIENT_SECRET'),
        'scope' => env('PASSPORT_AUTH_PASSWORD_SCOPE', ''),
    ],

    'registration' => [
        'default_status' => 1,
        'default_role_id' => null,
        'name_field' => 'name',
        'first_name_field' => 'first_name',
        'last_name_field' => 'last_name',
    ],

    'legacy_password' => [
        'enabled' => true,
        'algorithms' => ['sha1', 'md5'],
        'rehash_on_login' => true,
    ],

    'email_verification' => [
        'enabled' => env('PASSPORT_AUTH_EMAIL_VERIFICATION', false),
        'expiry' => 60,
        'verification_column' => 'email_verified_at',
        'route_prefix' => 'email',
        'url' => env('PASSPORT_AUTH_EMAIL_VERIFICATION_URL'),
    ],

    'password_reset' => [
        'enabled' => env('PASSPORT_AUTH_PASSWORD_RESET', true),
        'expiry' => 60,
        'url' => env('PASSPORT_AUTH_PASSWORD_RESET_URL'),
    ],

    'security' => [
        'login_throttle' => env('PASSPORT_AUTH_LOGIN_THROTTLE', true),
        'max_attempts' => (int) env('PASSPORT_AUTH_MAX_LOGIN_ATTEMPTS', 5),
        'lockout_minutes' => (int) env('PASSPORT_AUTH_LOCKOUT_MINUTES', 15),
        'track_login_history' => true,
        'track_devices' => true,
        'audit_events' => true,
        'ip_detection' => true,
        'store_ip_chain' => true,
    ],

    'otp' => [
        'enabled' => env('PASSPORT_AUTH_OTP_ENABLED', true),
        'length' => 6,
        'expiry' => 10,
        'max_attempts' => 5,
        'resend_cooldown' => 60,
    ],

    'social_login' => [
        'enabled' => env('PASSPORT_AUTH_SOCIAL_LOGIN', false),
        'providers' => ['google', 'facebook'],
        'stateless' => true,
    ],
];
