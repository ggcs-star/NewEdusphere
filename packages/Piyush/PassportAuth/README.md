# Piyush PassportAuth

Reusable Laravel Passport authentication/security package for Laravel 10 API applications.

## Scope

This package is designed to keep the host application's existing business logic intact while providing a reusable authentication/security layer.

### Included

- Laravel Passport OAuth2 password grant
- Access + refresh tokens
- Personal access tokens for social login
- Register/login/logout/logout-all
- Standard success/error/validation responses
- Consistent 401 unauthenticated responses
- Invalid credential handling
- Refresh-token error handling
- Legacy CodeIgniter SHA-1/MD5 password compatibility
- Automatic legacy password upgrade after successful login
- Email verification
- Forgot/reset password
- Change password
- Token revocation after password reset/change
- Login throttling/lockout
- Login history
- Device registration/tracking/revocation
- Client IP detection with IPv4/IPv6 support
- IP chain storage for proxy-aware deployments
- Security audit events
- Email OTP with expiry, attempt limit and resend cooldown
- Google/Facebook Socialite architecture
- Existing LMS-style `first_name`, `last_name`, `role_id`, `status`, `date_added`, `last_modified` compatibility
- Configurable user model/table and API prefix
- Upgrade migration for installations that already have the previous package tables

## Important: existing application logic

The package does not replace the application's business logic. It adds authentication/security services and database tables. Existing application controllers, models, integrations and business rules should remain outside this package.

## Installation

1. Copy the package to `packages/Piyush/PassportAuth` or install it through Composer.
2. Add the package repository if using a local path package.
3. Run `composer update` (or the equivalent package install command).
4. Ensure Laravel Passport is installed/configured.
5. Run `php artisan passport:install` if the application does not already have Passport clients/keys.
6. Publish config if required:

```bash
php artisan vendor:publish --tag=passport-auth-config
```

7. Run:

```bash
php artisan migrate
php artisan optimize:clear
```

## Environment

```env
PASSPORT_AUTH_API_PREFIX=api/v1/auth
PASSPORT_AUTH_USER_MODEL=App\\Models\\User
PASSPORT_AUTH_USERS_TABLE=users
PASSPORT_AUTH_PASSWORD_CLIENT_ID=
PASSPORT_AUTH_PASSWORD_CLIENT_SECRET=
PASSPORT_AUTH_PASSWORD_SCOPE=
PASSPORT_AUTH_EMAIL_VERIFICATION=false
PASSPORT_AUTH_EMAIL_VERIFICATION_URL=
PASSPORT_AUTH_PASSWORD_RESET=true
PASSPORT_AUTH_PASSWORD_RESET_URL=
PASSPORT_AUTH_LOGIN_THROTTLE=true
PASSPORT_AUTH_MAX_LOGIN_ATTEMPTS=5
PASSPORT_AUTH_LOCKOUT_MINUTES=15
PASSPORT_AUTH_OTP_ENABLED=true
PASSPORT_AUTH_SOCIAL_LOGIN=false
```

## Existing CodeIgniter/LMS passwords

Legacy SHA-1 and MD5 passwords are accepted when enabled. After a successful login, the legacy password is replaced with Laravel's secure password hash when `rehash_on_login` is enabled.

## IP detection / proxy handling

The package stores the resolved client IP using Laravel/Symfony's request client-IP resolution and can also store the resolved IP chain. For reverse proxies, Cloudflare, load balancers, or other proxy infrastructure, configure Laravel's trusted proxy handling correctly in the host application. The package should not blindly trust arbitrary `X-Forwarded-For` headers.

## API routes

Default prefix: `api/v1/auth`

### Public

- POST `/register`
- POST `/login`
- POST `/refresh`
- POST `/password/forgot`
- POST `/password/reset`
- POST `/otp/send`
- POST `/otp/verify`
- GET `/email/verify`
- POST `/email/verify`
- GET `/social/{provider}/redirect`
- GET `/social/{provider}/callback`

### Bearer protected

- GET `/me`
- POST `/logout`
- POST `/logout-all`
- POST `/password/change`
- POST `/email/resend`
- GET `/devices`
- POST `/devices/revoke`
- GET `/login-history`

## Response format

Success:

```json
{"success":true,"message":"...","data":{}}
```

Error:

```json
{"success":false,"message":"...","errors":{}}
```

Validation:

```json
{"success":false,"message":"Validation failed.","errors":{}}
```

## Social login

Google and Facebook are implemented through Laravel Socialite. The provider authenticates the user; Passport then issues the application's personal access token. Password-grant credentials are not fabricated for social accounts.

## Security tables

The package creates/uses:

- `passport_auth_login_histories`
- `passport_auth_devices`
- `passport_auth_password_resets`
- `passport_auth_email_verifications`
- `passport_auth_otps`
- `passport_auth_social_accounts`
- `passport_auth_security_audits`

## Production notes

- Never expose the Passport password-grant client secret in source control or public chat.
- Configure HTTPS in production.
- Configure trusted proxies correctly when deployed behind a proxy/load balancer.
- Configure mail before enabling email verification, password reset or OTP email.
- Configure Google/Facebook credentials only when social login is enabled.
- Back up the database before applying package migrations to an existing LMS database.

## Role/Permission

Role/permission/RBAC is intentionally not included in this authentication package phase. It can be layered on top of the authenticated `User` model without changing the Passport token flow.
