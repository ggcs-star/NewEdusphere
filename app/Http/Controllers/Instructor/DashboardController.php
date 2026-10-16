<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\DashboardAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardAnalyticsService $analytics)
    {
    }

    public function index(Request $request): View
    {
        $instructorId = current_admin_user()->id;
        $courses = Course::where('user_id', $instructorId)->withCount('enrollments')->get();

        return view('instructor.dashboard', [
            'stats' => [
                'total' => $courses->count(),
                'active' => $courses->where('status', 'active')->count(),
                'pending' => $courses->where('status', 'pending')->count(),
                'rejected' => $courses->where('status', 'rejected')->count(),
                'students' => $courses->sum('enrollments_count'),
            ],
            'recentCourses' => $courses->sortByDesc('created_at')->take(5)->values(),
            'analytics' => $this->analytics->build($instructorId, $request->get('period', 'week')),
        ]);
    }
}
