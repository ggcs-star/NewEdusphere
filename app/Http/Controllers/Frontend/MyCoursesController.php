<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MyCoursesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyCoursesController extends Controller
{
    public function __construct(private readonly MyCoursesService $myCourses)
    {
    }

    public function index(Request $request): View|RedirectResponse
    {
        // Resolve the authenticated user from the existing custom session.
        if (
            !$request->session()->has('user_token') ||
            !$request->session()->has('user')
        ) {
            return redirect()
                ->route('login')
                ->with('error', 'Please log in to view your courses.');
        }

        $sessionUser = $request->session()->get('user');

        $userId = is_object($sessionUser)
            ? ($sessionUser->id ?? null)
            : (is_array($sessionUser)
                ? ($sessionUser['id'] ?? null)
                : null);

        $user = $userId ? User::query()->find($userId) : null;

        if (!$user) {
            return redirect()
                ->route('login')
                ->with('error', 'Your session is invalid. Please log in again.');
        }

        $courses = $this->myCourses->enrolledCourses($user);
        $summary = $this->myCourses->summary($user);

        return view('frontend.my-courses.index', [
            'user' => $user,
            'courses' => $courses,
            'totalEnrollments' => $summary['total_enrollments'],
            'totalSpent' => $summary['total_spent'],
            'successfulPayments' => $summary['successful_payments'],
        ]);
    }
}
