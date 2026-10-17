
<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnrollmentController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LessonController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\RevenueController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\MyCoursesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Controllers\Instructor\CourseController as InstructorCourseController;
use App\Http\Controllers\Instructor\DashboardController as InstructorDashboardController;
use App\Http\Controllers\Instructor\RevenueController as InstructorRevenueController;
use App\Http\Controllers\Frontend\CourseController as FrontendCourseController;
use App\Http\Controllers\Frontend\LessonProgressController;
use App\Http\Controllers\Frontend\QuizController;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Homepage
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get(
    '/courses/{courseId}/lessons/{lessonId}/progress',
    [LessonProgressController::class, 'show']
)->name('courses.lessons.progress.show');

Route::post(
    '/courses/{courseId}/lessons/{lessonId}/progress',
    [LessonProgressController::class, 'save']
)->name('courses.lessons.progress.save');
/*
|--------------------------------------------------------------------------
| Public Frontend Pages
|--------------------------------------------------------------------------
*/

Route::get(
    '/courses/{courseId}/lessons/{lessonId}/quiz',
    [QuizController::class, 'show']
)->whereNumber('courseId')
 ->whereNumber('lessonId')
 ->name('courses.quizzes.show');

Route::post(
    '/courses/{courseId}/lessons/{lessonId}/quiz',
    [QuizController::class, 'submit']
)->whereNumber('courseId')
 ->whereNumber('lessonId')
 ->name('courses.quizzes.submit');

Route::get('/courses', function (Request $request) {
    $query = Course::query()
        ->with(['instructor', 'category']);

    // Search by course title or short description.
    if ($request->filled('q')) {
        $search = trim((string) $request->query('q'));

        $query->where(function ($builder) use ($search) {
            $builder->where('title', 'like', '%' . $search . '%')
                ->orWhere(
                    'short_description',
                    'like',
                    '%' . $search . '%'
                );
        });
    }

    // Filter by category.
    if ($request->filled('category')) {
        $query->where('category_id', $request->query('category'));
    }

    $courses = $query
        ->latest()
        ->paginate(12)
        ->withQueryString();

    $categories = Category::query()
        ->orderBy('name')
        ->get();

    return view(
        'frontend.courses.index',
        compact('courses', 'categories')
    );
})->name('courses.index');

Route::get('/courses/{course}', function (Course $course) {
    $course->load(['instructor', 'category']);

    return view('frontend.courses.show', compact('course'));
})->whereNumber('course')->name('courses.show');
Route::get('/home/course/{slug}/{id}', [
    FrontendCourseController::class,
    'show',
])->whereNumber('id')->name('home.course.show');
Route::get('/categories', function () {
    $categories = Category::query()
        ->withCount('children')
        ->orderBy('name')
        ->get();

    return view('frontend.categories.index', compact('categories'));
})->name('categories.index');

Route::view('/about', 'frontend.about')->name('about');

Route::view('/terms', 'frontend.legal', ['heading' => 'Terms & Conditions', 'field' => 'terms_and_condition'])->name('terms');
Route::view('/privacy', 'frontend.legal', ['heading' => 'Privacy Policy', 'field' => 'privacy_policy'])->name('privacy');

Route::get('/contact', function () {
    return view('frontend.contact');
})->name('contact');

Route::post('/contact', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:190'],
        'subject' => ['nullable', 'string', 'max:190'],
        'message' => ['required', 'string', 'max:5000'],
    ]);

    return back()->with(
        'success',
        'Your message form was submitted. Connect email or database storage before launch to retain the message.'
    );
})->name('contact.submit');

/*
|--------------------------------------------------------------------------
| Guest Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [
        AuthController::class,
        'showLogin',
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login',
    ])->name('login.submit');

    Route::get('/register', [
        AuthController::class,
        'showRegister',
    ])->name('register');

    Route::post('/register', [
        AuthController::class,
        'register',
    ])->name('register.submit');
});

/*
|--------------------------------------------------------------------------
| Password Recovery
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

Route::get('/reset-password', [
    AuthController::class,
    'showResetPassword',
])->name('password.reset');

/*
|--------------------------------------------------------------------------
| Device Verification
|--------------------------------------------------------------------------
*/

Route::get('/verify-device', [
    AuthController::class,
    'verifyDevice',
])->name('verify.device');

/*
|--------------------------------------------------------------------------
| Establish Web Session
|--------------------------------------------------------------------------
*/

Route::post('/web/session', [
    AuthController::class,
    'establishSession',
])->middleware('device.identification')
    ->name('web.session');

/*
|--------------------------------------------------------------------------
| Authenticated Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware('web.auth')->group(function () {

    // User dashboard.
    Route::get('/dashboard', function () {
        $user = current_admin_user();

        if ($user?->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user?->is_instructor) {
            return redirect()->route('instructor.dashboard');
        }

        if ($user?->is_instructor) {
            return redirect()->route('instructor.dashboard');
        }

        $userId = $user?->id;

        $enrolledIds = $userId
            ? DB::table('enrollments')
                ->where('user_id', $userId)
                ->pluck('course_id')
            : collect();

        $courses = $enrolledIds->isNotEmpty()
            ? Course::with(['instructor', 'category'])
                ->whereIn('id', $enrolledIds)
                ->latest()
                ->take(6)
                ->get()
            : collect();

        $enrolledCount = $enrolledIds->count();

        return view(
            'frontend.dashboard.index',
            compact('user', 'courses', 'enrolledCount')
        );
    })->name('dashboard');

    Route::get('/me', [
        AuthController::class,
        'me',
    ])->name('me');

    Route::post('/logout', [
        AuthController::class,
        'logout',
    ])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Checkout Payment Actions
    |--------------------------------------------------------------------------
    | The checkout page can be viewed by guests.
    | Actual payment actions require an authenticated user.
    | Controllers must still validate ownership and payment signatures.
    */

    Route::post('/checkout/pay', [
        CheckoutController::class,
        'pay',
    ])->name('checkout.pay');

    Route::post('/checkout/verify', [
        CheckoutController::class,
        'verify',
    ])->name('checkout.verify');

    /*
    |--------------------------------------------------------------------------
    | My Courses
    |--------------------------------------------------------------------------
    */

    Route::get('/my-courses', [
        MyCoursesController::class,
        'index',
    ])->name('my-courses.index');

    /*
    |--------------------------------------------------------------------------
    | Purchase History
    |--------------------------------------------------------------------------
    */

    Route::get('/purchase-history', function () {
        $user = current_admin_user();

        abort_unless($user, 403);

   $payments = DB::table('payments')
    ->where('user_id', $user->id)
->orderByDesc('id')
    ->get();

        return view(
            'frontend.purchase-history.index',
            compact('payments', 'user')
        );
    })->name('purchase-history.index');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Learner Cart
|--------------------------------------------------------------------------
| Cart operations are available to guests.
| CartController should store guest cart items in the session.
|--------------------------------------------------------------------------
*/

Route::prefix('cart')->name('cart.')->group(function () {

    Route::get('/', [
        CartController::class,
        'index',
    ])->name('index');

    Route::post('/add/{courseId}', [
        CartController::class,
        'add',
    ])->whereNumber('courseId')->name('add');

    Route::post('/buy-now/{courseId}', [
        CartController::class,
        'buyNow',
    ])->whereNumber('courseId')->name('buy-now');

    Route::delete('/remove/{courseId}', [
        CartController::class,
        'remove',
    ])->whereNumber('courseId')->name('remove');

    Route::delete('/clear', [
        CartController::class,
        'clear',
    ])->name('clear');
});

/*
|--------------------------------------------------------------------------
| Checkout Page
|--------------------------------------------------------------------------
| Guest can view checkout.
| Payment POST routes are defined inside web.auth above.
|--------------------------------------------------------------------------
*/

Route::get('/checkout', [
    CheckoutController::class,
    'index',
])->name('checkout.index');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['web.auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard.
        Route::get('/dashboard', [
            DashboardController::class,
            'index',
        ])->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Categories
        |--------------------------------------------------------------------------
        */

        Route::get('/categories', [
            CategoryController::class,
            'index',
        ])->name('categories.index');

        Route::post('/categories', [
            CategoryController::class,
            'store',
        ])->name('categories.store');

        Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::put('/categories/{category}', [
            CategoryController::class,
            'update',
        ])->name('categories.update');

        Route::delete('/categories/{category}', [
            CategoryController::class,
            'destroy',
        ])->name('categories.destroy');

        /*
        |--------------------------------------------------------------------------
        | Courses
        |--------------------------------------------------------------------------
        */

        Route::get('/courses', [
            CourseController::class,
            'index',
        ])->name('courses.index');

        Route::get('/courses/pending', [
            CourseController::class,
            'pending',
        ])->name('courses.pending');

        Route::get('/courses/create', [
            CourseController::class,
            'create',
        ])->name('courses.create');

        Route::post('/courses', [
            CourseController::class,
            'store',
        ])->name('courses.store');

        Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/courses/{course}/edit', [
            CourseController::class,
            'edit',
        ])->name('courses.edit');

        Route::put('/courses/{course}', [
            CourseController::class,
            'update',
        ])->name('courses.update');

        Route::delete('/courses/{course}', [
            CourseController::class,
            'destroy',
        ])->name('courses.destroy');

        Route::post('/courses/{course}/approve', [
            CourseController::class,
            'approve',
        ])->name('courses.approve');

        Route::post('/courses/{course}/reject', [
            CourseController::class,
            'reject',
        ])->name('courses.reject');

        Route::patch('/courses/{course}/status', [
            CourseController::class,
            'updateStatus',
        ])->name('courses.update-status');

        Route::get('/courses/{course}/builder', [
            CourseController::class,
            'builder',
        ])->name('courses.builder');

        /*
        |--------------------------------------------------------------------------
        | Course Sections
        |--------------------------------------------------------------------------
        */

        Route::post('/courses/{course}/sections', [
            SectionController::class,
            'store',
        ])->name('sections.store');

        Route::post('/courses/{course}/sections/reorder', [
            SectionController::class,
            'reorder',
        ])->name('sections.reorder');

        Route::put('/sections/{section}', [
            SectionController::class,
            'update',
        ])->name('sections.update');

        Route::delete('/sections/{section}', [
            SectionController::class,
            'destroy',
        ])->name('sections.destroy');

        /*
        |--------------------------------------------------------------------------
        | Lessons
        |--------------------------------------------------------------------------
        */

        Route::post('/sections/{section}/lessons', [
            LessonController::class,
            'store',
        ])->name('lessons.store');

        Route::post('/sections/{section}/lessons/reorder', [
            LessonController::class,
            'reorder',
        ])->name('lessons.reorder');

        Route::put('/lessons/{lesson}', [
            LessonController::class,
            'update',
        ])->name('lessons.update');

        Route::delete('/lessons/{lesson}', [
            LessonController::class,
            'destroy',
        ])->name('lessons.destroy');

        /*
        |--------------------------------------------------------------------------
        | Lesson Questions
        |--------------------------------------------------------------------------
        */

        Route::post('/lessons/{lesson}/questions', [
            QuestionController::class,
            'store',
        ])->name('questions.store');

        Route::put('/questions/{question}', [
            QuestionController::class,
            'update',
        ])->name('questions.update');

        Route::delete('/questions/{question}', [
            QuestionController::class,
            'destroy',
        ])->name('questions.destroy');

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        Route::get('/users', [
            UserController::class,
            'index',
        ])->name('users.index');

        Route::get('/users/create', [
            UserController::class,
            'create',
        ])->name('users.create');

        Route::post('/users', [
            UserController::class,
            'store',
        ])->name('users.store');

        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [
            UserController::class,
            'edit',
        ])->name('users.edit');

        Route::put('/users/{user}', [
            UserController::class,
            'update',
        ])->name('users.update');

        Route::patch('/users/{user}/status', [
            UserController::class,
            'updateStatus',
        ])->name('users.update-status');

        Route::delete('/users/{user}', [
            UserController::class,
            'destroy',
        ])->name('users.destroy');

        /*
        |--------------------------------------------------------------------------
        | Enrollments
        |--------------------------------------------------------------------------
        */

        Route::get('/enrollments', [
            EnrollmentController::class,
            'index',
        ])->name('enrollments.index');
    Route::get('/enrollments/create', [EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::get('/enrollments/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');
    Route::get('/enrollments/{enrollment}/edit', [EnrollmentController::class, 'edit'])->name('enrollments.edit');
    Route::put('/enrollments/{enrollment}', [EnrollmentController::class, 'update'])->name('enrollments.update');
    Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');

        /*
        |--------------------------------------------------------------------------
        | Revenue
        |--------------------------------------------------------------------------
        */

        Route::get('/revenue/{tab?}', [
            RevenueController::class,
            'index',
        ])->name('revenue.index');
    Route::delete('/revenue/{payment}', [RevenueController::class, 'destroy'])->name('revenue.destroy');
    Route::patch('/revenue/{payment}/mark-paid', [RevenueController::class, 'markPaid'])->name('revenue.mark-paid');

        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */

        Route::get('/settings/{tab?}', [
            SettingController::class,
            'index',
        ])->name('settings.index');

        Route::post('/settings/{tab}', [
            SettingController::class,
            'update',
        ])->name('settings.update');
    
    // Languages (settings sub-feature)
    Route::post('/languages', [LanguageController::class, 'store'])->name('languages.store');
    Route::delete('/languages/{language}', [LanguageController::class, 'destroy'])->name('languages.destroy');

    // Profile (the admin's own account)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['web.auth', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/revenue', [InstructorRevenueController::class, 'index'])->name('revenue.index');

    // Profile (the instructor's own account)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Own courses
    Route::get('/courses', [InstructorCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [InstructorCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [InstructorCourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [InstructorCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [InstructorCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::get('/courses/{course}/builder', [CourseController::class, 'builder'])->name('courses.builder');
        /*
        |--------------------------------------------------------------------------
        | Course Sections
        |--------------------------------------------------------------------------
        */
    // Course content builder (shared with admin, ownership-checked)
    Route::post('/courses/{course}/sections', [SectionController::class, 'store'])->name('sections.store');
    Route::post('/courses/{course}/sections/reorder', [SectionController::class, 'reorder'])->name('sections.reorder');
    Route::put('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
    Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');

        /*
        |--------------------------------------------------------------------------
        | Lessons
        |--------------------------------------------------------------------------
        */

        Route::post('/sections/{section}/lessons', [
            LessonController::class,
            'store',
        ])->name('lessons.store');

        Route::post('/sections/{section}/lessons/reorder', [
            LessonController::class,
            'reorder',
        ])->name('lessons.reorder');

        Route::put('/lessons/{lesson}', [
            LessonController::class,
            'update',
        ])->name('lessons.update');

        Route::delete('/lessons/{lesson}', [
            LessonController::class,
            'destroy',
        ])->name('lessons.destroy');

        /*
        |--------------------------------------------------------------------------
        | Lesson Questions
        |--------------------------------------------------------------------------
        */

        Route::post('/lessons/{lesson}/questions', [
            QuestionController::class,
            'store',
        ])->name('questions.store');

        Route::put('/questions/{question}', [
            QuestionController::class,
            'update',
        ])->name('questions.update');

        Route::delete('/questions/{question}', [
            QuestionController::class,
            'destroy',
        ])->name('questions.destroy');

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        Route::get('/users', [
            UserController::class,
            'index',
        ])->name('users.index');

        Route::get('/users/create', [
            UserController::class,
            'create',
        ])->name('users.create');

        Route::post('/users', [
            UserController::class,
            'store',
        ])->name('users.store');

        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [
            UserController::class,
            'edit',
        ])->name('users.edit');

        Route::put('/users/{user}', [
            UserController::class,
            'update',
        ])->name('users.update');

        Route::patch('/users/{user}/status', [
            UserController::class,
            'updateStatus',
        ])->name('users.update-status');

        Route::delete('/users/{user}', [
            UserController::class,
            'destroy',
        ])->name('users.destroy');

        /*
        |--------------------------------------------------------------------------
        | Enrollments
        |--------------------------------------------------------------------------
        */

        Route::get('/enrollments', [
            EnrollmentController::class,
            'index',
        ])->name('enrollments.index');
    Route::get('/enrollments/create', [EnrollmentController::class, 'create'])->name('enrollments.create');
    Route::post('/enrollments', [EnrollmentController::class, 'store'])->name('enrollments.store');
    Route::get('/enrollments/{enrollment}', [EnrollmentController::class, 'show'])->name('enrollments.show');
    Route::get('/enrollments/{enrollment}/edit', [EnrollmentController::class, 'edit'])->name('enrollments.edit');
    Route::put('/enrollments/{enrollment}', [EnrollmentController::class, 'update'])->name('enrollments.update');
    Route::delete('/enrollments/{enrollment}', [EnrollmentController::class, 'destroy'])->name('enrollments.destroy');

        /*
        |--------------------------------------------------------------------------
        | Revenue
        |--------------------------------------------------------------------------
        */

        Route::get('/revenue/{tab?}', [
            RevenueController::class,
            'index',
        ])->name('revenue.index');
    Route::delete('/revenue/{payment}', [RevenueController::class, 'destroy'])->name('revenue.destroy');
    Route::patch('/revenue/{payment}/mark-paid', [RevenueController::class, 'markPaid'])->name('revenue.mark-paid');

        /*
        |--------------------------------------------------------------------------
        | Settings
        |--------------------------------------------------------------------------
        */

        Route::get('/settings/{tab?}', [
            SettingController::class,
            'index',
        ])->name('settings.index');

        Route::post('/settings/{tab}', [
            SettingController::class,
            'update',
        ])->name('settings.update');
    
    // Languages (settings sub-feature)
    Route::post('/languages', [LanguageController::class, 'store'])->name('languages.store');
    Route::delete('/languages/{language}', [LanguageController::class, 'destroy'])->name('languages.destroy');
});

Route::middleware(['web.auth', 'instructor'])->prefix('instructor')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/revenue', [InstructorRevenueController::class, 'index'])->name('revenue.index');

    // Own courses
    Route::get('/courses', [InstructorCourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/create', [InstructorCourseController::class, 'create'])->name('courses.create');
    Route::post('/courses', [InstructorCourseController::class, 'store'])->name('courses.store');
    Route::get('/courses/{course}/edit', [InstructorCourseController::class, 'edit'])->name('courses.edit');
    Route::put('/courses/{course}', [InstructorCourseController::class, 'update'])->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    Route::get('/courses/{course}/builder', [CourseController::class, 'builder'])->name('courses.builder');

    // Course content builder (shared with admin, ownership-checked)
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
});
