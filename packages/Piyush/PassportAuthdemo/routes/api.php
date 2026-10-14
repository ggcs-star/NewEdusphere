<?php

use Illuminate\Support\Facades\Route;
use Piyush\PassportAuth\Http\Controllers\AuthController;
use Piyush\PassportAuth\Http\Middleware\HandlePassportAuthErrors;

Route::prefix(config('passport-auth.api_prefix', 'api/v1/auth'))
    ->middleware(HandlePassportAuthErrors::class)
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Public Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::post('/register', [
            AuthController::class,
            'register',
        ])->name('passport-auth.register');

        Route::post('/login', [
            AuthController::class,
            'login',
        ])->name('passport-auth.login');

        Route::post('/refresh', [
            AuthController::class,
            'refresh',
        ])->name('passport-auth.refresh');

        /*
        |--------------------------------------------------------------------------
        | Protected Authentication Routes
        |--------------------------------------------------------------------------
        */

        Route::middleware('auth:api')->group(function () {

            Route::get('/me', [
                AuthController::class,
                'me',
            ])->name('passport-auth.me');

            Route::post('/logout', [
                AuthController::class,
                'logout',
            ])->name('passport-auth.logout');

        });
    });