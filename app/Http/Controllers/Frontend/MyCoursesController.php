<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyCoursesController extends Controller
{
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

        // Fetch only courses this user is enrolled in.
        $courses = Course::query()
            ->with([
                'instructor',
                'category',
                'enrollments' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                },
                'payments' => function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                        ->latest('id');
                },
            ])
            ->withCount(['sections', 'lessons'])
            ->where('status', 'active')
            ->whereHas('enrollments', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest('id')
            ->paginate(9)
            ->withQueryString();

        // Summary cards are based on the current user's own records.
        $totalEnrollments = Enrollment::query()
            ->where('user_id', $user->id)
            ->count();

        $totalSpent = Payment::query()
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');

        $successfulPayments = Payment::query()
            ->where('user_id', $user->id)
            ->where('status', 'paid')
            ->count();

        return view('frontend.my-courses.index', [
            'user' => $user,
            'courses' => $courses,
            'totalEnrollments' => $totalEnrollments,
            'totalSpent' => $totalSpent,
            'successfulPayments' => $successfulPayments,
        ]);
    }
}
