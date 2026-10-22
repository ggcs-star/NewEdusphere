<?php

use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CertificateController;
use App\Http\Controllers\Api\V1\CheckoutController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\MyCoursesController;
use App\Http\Controllers\Api\V1\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| User-side API v1 (mirrors the authenticated web routes)
|--------------------------------------------------------------------------
| Login/register/etc already live under api/v1/auth via the PassportAuth
| package — this group is everything a logged-in learner does afterwards.
| Guests may browse the catalog; everything else requires a Passport
| Bearer token (auth:api), the same guard /api/v1/auth/login issues.
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('api.v1.')->group(function () {

    // Catalog — browsable by guests.
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/{slug}/{id}', [CourseController::class, 'show'])
            ->whereNumber('id')
            ->name('show');
    });

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');
    });

    // Everything below acts on "my" account — requires a Passport token.
    Route::middleware('auth:api')->group(function () {

        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartController::class, 'index'])->name('index');
            Route::post('/add/{courseId}', [CartController::class, 'add'])->whereNumber('courseId')->name('add');
            Route::delete('/remove/{courseId}', [CartController::class, 'remove'])->whereNumber('courseId')->name('remove');
            Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
        });

        Route::prefix('checkout')->name('checkout.')->group(function () {
            Route::post('/preview', [CheckoutController::class, 'preview'])->name('preview');
            Route::post('/pay', [CheckoutController::class, 'pay'])->name('pay');
            Route::post('/verify', [CheckoutController::class, 'verify'])->name('verify');
        });

        Route::prefix('my-courses')->name('my-courses.')->group(function () {
            Route::get('/', [MyCoursesController::class, 'index'])->name('index');
            Route::get('/purchase-history', [MyCoursesController::class, 'purchaseHistory'])->name('purchase-history');
        });

        Route::get('/dashboard', [MyCoursesController::class, 'dashboard'])->name('dashboard');

        Route::prefix('certificates')->name('certificates.')->group(function () {
            Route::get('/', [CertificateController::class, 'index'])->name('index');
            Route::get('/{certificate}', [CertificateController::class, 'show'])->whereNumber('certificate')->name('show');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::post('/', [ProfileController::class, 'update'])->name('update');
        });
    });
});
