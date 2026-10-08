<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;


/*
|--------------------------------------------------------------------------
| Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');


/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    Route::get('/login', [
        AuthController::class,
        'showLogin'
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login'
    ])->name('login.submit');


    /*
    |--------------------------------------------------------------------------
    | Register
    |--------------------------------------------------------------------------
    */

    Route::get('/register', [
        AuthController::class,
        'showRegister'
    ])->name('register');

    Route::post('/register', [
        AuthController::class,
        'register'
    ])->name('register.submit');

});


/*
|--------------------------------------------------------------------------
| Forgot Password
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');


/*
|--------------------------------------------------------------------------
| Reset Password
|--------------------------------------------------------------------------
|
| Password reset email opens:
|
| /reset-password?token=XXXXX&email=XXXXX
|
*/
Route::get('/reset-password', [
    AuthController::class,
    'showResetPassword'
])->name('password.reset');
/*
|--------------------------------------------------------------------------
| Device Verification
|--------------------------------------------------------------------------
*/

Route::get('/verify-device', [
    AuthController::class,
    'verifyDevice'
])->name('verify.device');


/*
|--------------------------------------------------------------------------
| Establish Web Session
|--------------------------------------------------------------------------
*/

Route::post('/web/session', [
    AuthController::class,
    'establishSession'
])
->middleware('device.identification')
->name('web.session');


/*
|--------------------------------------------------------------------------
| Authenticated Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware('web.auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', function () {
        return view('dashboard.index');
    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Current User
    |--------------------------------------------------------------------------
    */

    Route::get('/me', [
        AuthController::class,
        'me'
    ])->name('me');


    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [
        AuthController::class,
        'logout'
    ])->name('logout');

});