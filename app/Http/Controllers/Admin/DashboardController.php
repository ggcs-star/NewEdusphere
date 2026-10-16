<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use App\Services\DashboardAnalyticsService;
use App\Services\TrendStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly TrendStatsService $trends,
        private readonly DashboardAnalyticsService $analytics,
    ) {
    }

    public function index(Request $request): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'categories' => $this->trends->forModel(Category::class, fn ($q) => $q->topLevel()),
                'activeCourses' => $this->trends->forModel(Course::class, fn ($q) => $q->where('status', 'active')),
                'students' => $this->trends->forModel(User::class, fn ($q) => $q->where('role', 'user')),
            ],
            'pendingCourses' => Course::where('status', 'pending')->count(),
            'analytics' => $this->analytics->build(null, $request->get('period', 'week')),
        ]);
    }
}
