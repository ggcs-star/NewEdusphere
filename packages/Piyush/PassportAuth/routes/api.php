<?php
use Illuminate\Support\Facades\Route;
use Piyush\PassportAuth\Http\Controllers\AuthController;
use Piyush\PassportAuth\Http\Middleware\HandlePassportAuthErrors;
use Piyush\PassportAuth\Http\Middleware\LoginThrottle;

Route::prefix(config('passport-auth.api_prefix', 'api/v1/auth'))
    ->middleware(HandlePassportAuthErrors::class)
    ->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('passport-auth.register');
        Route::post('/login', [AuthController::class, 'login'])->middleware(LoginThrottle::class)->name('passport-auth.login');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('passport-auth.refresh');
        Route::post('/password/forgot', [AuthController::class, 'forgotPassword'])->name('passport-auth.password.forgot');
        Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('passport-auth.password.reset');
        Route::post('/otp/send', [AuthController::class, 'sendOtp'])->name('passport-auth.otp.send');
        Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('passport-auth.otp.verify');
        Route::get('/email/verify', [AuthController::class, 'verifyEmail'])
        ->name('passport-auth.email.verify.link');
        Route::post('/email/verify', [AuthController::class, 'verifyEmail'])->name('passport-auth.email.verify');

        Route::get('/social/{provider}/redirect', [AuthController::class, 'socialRedirect'])->name('passport-auth.social.redirect');
        Route::get('/social/{provider}/callback', [AuthController::class, 'socialCallback'])->name('passport-auth.social.callback');

        Route::middleware('auth:api')->group(function () {
            Route::get('/me', [AuthController::class, 'me'])->name('passport-auth.me');
            Route::post('/logout', [AuthController::class, 'logout'])->name('passport-auth.logout');
            Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('passport-auth.logout-all');
            Route::post('/password/change', [AuthController::class, 'changePassword'])->name('passport-auth.password.change');
            Route::post('/email/resend', [AuthController::class, 'resendVerification'])->name('passport-auth.email.resend');
            Route::get('/devices', [AuthController::class, 'devices'])->name('passport-auth.devices');
            Route::post('/devices/revoke', [AuthController::class, 'revokeDevice'])->name('passport-auth.devices.revoke');
            Route::get('/login-history', [AuthController::class, 'loginHistory'])->name('passport-auth.login-history');
        });
    });
