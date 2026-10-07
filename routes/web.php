<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\TempLoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// TEMPORARY — remove once the real auth flow (login/register/forgot) is merged.
Route::get('/login', [TempLoginController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [TempLoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [TempLoginController::class, 'destroy'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/pending', [CourseController::class, 'pending'])->name('courses.pending');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('/courses/{course}/approve', [CourseController::class, 'approve'])->name('courses.approve');
    Route::post('/courses/{course}/reject', [CourseController::class, 'reject'])->name('courses.reject');
    Route::patch('/courses/{course}/status', [CourseController::class, 'updateStatus'])->name('courses.update-status');
    Route::get('/courses/{course}/builder', [CourseController::class, 'builder'])->name('courses.builder');

    // Course content builder
    Route::post('/courses/{course}/sections', [SectionController::class, 'store'])->name('sections.store');
    Route::post('/courses/{course}/sections/reorder', [SectionController::class, 'reorder'])->name('sections.reorder');
    Route::put('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
    Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');

    Route::post('/sections/{section}/lessons', [LessonController::class, 'store'])->name('lessons.store');
    Route::post('/sections/{section}/lessons/reorder', [LessonController::class, 'reorder'])->name('lessons.reorder');
    Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('lessons.destroy');

    Route::post('/lessons/{lesson}/questions', [QuestionController::class, 'store'])->name('questions.store');
    Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Enrollment history
    Route::get('/enrollments', [EnrollmentController::class, 'index'])->name('enrollments.index');

    // Revenue
    Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue.index');

    // Settings
    Route::get('/settings/{tab?}', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/{tab}', [SettingController::class, 'update'])->name('settings.update');
});
