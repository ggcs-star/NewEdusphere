<?php
namespace Piyush\PassportAuth\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Piyush\PassportAuth\Contracts\AuthenticationServiceInterface;
use Piyush\PassportAuth\Http\Requests\ChangePasswordRequest;
use Piyush\PassportAuth\Http\Requests\ForgotPasswordRequest;
use Piyush\PassportAuth\Http\Requests\LoginRequest;
use Piyush\PassportAuth\Http\Requests\RefreshTokenRequest;
use Piyush\PassportAuth\Http\Requests\RegisterRequest;
use Piyush\PassportAuth\Http\Requests\ResetPasswordRequest;
use Piyush\PassportAuth\Http\Requests\VerifyEmailRequest;
use Piyush\PassportAuth\Support\AuthResponse;
use Piyush\PassportAuth\Services\OtpService;
use Piyush\PassportAuth\Services\PasswordService;
use Piyush\PassportAuth\Http\Requests\VerifyOtpRequest;
use Piyush\PassportAuth\Http\Requests\SendOtpRequest;
use Piyush\PassportAuth\Services\SocialAuthService;
use Piyush\PassportAuth\Services\SecurityService;

class AuthController extends Controller
{
    public function __construct(protected AuthenticationServiceInterface $authenticationService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        return AuthResponse::success('Registration successful.', $this->authenticationService->register($request->validated()), 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return AuthResponse::success('Login successful.', $this->authenticationService->login($request->validated()));
    }

    public function refresh(RefreshTokenRequest $request): JsonResponse
    {
        return AuthResponse::success('Token refreshed successfully.', $this->authenticationService->refresh($request->validated('refresh_token')));
    }

    public function logout(): JsonResponse
    {
        $this->authenticationService->logout();
        return AuthResponse::success('Logout successful.');
    }

    public function logoutAll(): JsonResponse
    {
        $this->authenticationService->logoutAll();
        return AuthResponse::success('All sessions have been logged out.');
    }

    public function me(): JsonResponse
    {
        $user = $this->authenticationService->user();
        return $user ? AuthResponse::success('User retrieved successfully.', ['user' => $user]) : AuthResponse::unauthenticated();
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $this->authenticationService->changePassword($request->string('current_password')->toString(), $request->string('password')->toString());
        return AuthResponse::success('Password changed successfully.');
    }

    public function forgotPassword(ForgotPasswordRequest $request, PasswordService $passwords): JsonResponse
    {
        $passwords->forgot($request->string('email')->toString());
        return AuthResponse::success('If the email is registered, a password reset link has been sent.');
    }

    public function resetPassword(ResetPasswordRequest $request, PasswordService $passwords): JsonResponse
    {
        $passwords->reset($request->string('email')->toString(), $request->string('token')->toString(), $request->string('password')->toString());
        return AuthResponse::success('Password reset successfully.');
    }

    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        $this->authenticationService->verifyEmail($request->string('token')->toString());
        return AuthResponse::success('Email verified successfully.');
    }

    public function devices(SecurityService $security): JsonResponse
    {
        $user = $this->authenticationService->user();
        if (!$user) return AuthResponse::unauthenticated();
        return AuthResponse::success('Devices retrieved successfully.', ['devices' => $security->devices($user)]);
    }

    public function revokeDevice(\Illuminate\Http\Request $request, SecurityService $security): JsonResponse
    {
        $user = $this->authenticationService->user();
        if (!$user) return AuthResponse::unauthenticated();
        $deviceId = $request->input('device_id');
        $security->revokeDevice($user, $deviceId);
        $security->recordAudit($user->getKey(), 'device_revoked', ['device_id' => $deviceId]);
        return AuthResponse::success('Device revoked successfully.');
    }

    public function loginHistory(SecurityService $security): JsonResponse
    {
        $user = $this->authenticationService->user();
        if (!$user) return AuthResponse::unauthenticated();
        $history = \Illuminate\Support\Facades\DB::table('passport_auth_login_histories')->where('user_id', $user->getKey())->orderByDesc('created_at')->limit(100)->get();
        return AuthResponse::success('Login history retrieved successfully.', ['history' => $history]);
    }

    public function resendVerification(): JsonResponse
    {
        $user = $this->authenticationService->user();
        if (!$user) return AuthResponse::unauthenticated();
        $this->authenticationService->sendEmailVerification($user);
        return AuthResponse::success('Verification email sent.');
    }

    public function sendOtp(SendOtpRequest $request, OtpService $otp): JsonResponse
    {
        $otp->send($request->string('email')->toString());
        return AuthResponse::success('OTP sent successfully.');
    }

    public function verifyOtp(VerifyOtpRequest $request, OtpService $otp): JsonResponse
    {
        $otp->verify($request->string('email')->toString(), $request->string('otp')->toString());
        return AuthResponse::success('OTP verified successfully.');
    }

    public function socialRedirect(string $provider, SocialAuthService $social)
    {
        return $social->redirect($provider);
    }

    public function socialCallback(string $provider, SocialAuthService $social): JsonResponse
    {
        return AuthResponse::success('Social login successful.', $social->callback($provider));
    }
}
