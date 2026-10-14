<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $courses = Course::where('user_id', Auth::id())->withCount('enrollments')->get();

        return view('instructor.dashboard', [
            'stats' => [
                'total' => $courses->count(),
                'active' => $courses->where('status', 'active')->count(),
                'pending' => $courses->where('status', 'pending')->count(),
                'rejected' => $courses->where('status', 'rejected')->count(),
                'students' => $courses->sum('enrollments_count'),
            ],
            'recentCourses' => $courses->sortByDesc('created_at')->take(5)->values(),
        ]);
    }
}
