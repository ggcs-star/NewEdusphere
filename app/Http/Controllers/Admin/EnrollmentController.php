<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\TrendStatsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends BaseAdminController
{
    public function __construct(
        private readonly TrendStatsService $trends,
    ) {
    }

    public function index(Request $request): View
    {
        $query = Enrollment::with(['user', 'course'])->latest();

        if ($courseId = $request->get('course_id')) {
            if ($courseId !== 'all') {
                $query->where('course_id', $courseId);
            }
        }

        if ($search = $request->get('search')) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        if ($priceFilter = $request->get('price_filter')) {
            if ($priceFilter === 'free') {
                $query->whereHas('course', fn ($q) => $q->where('is_free', true));
            } elseif ($priceFilter === 'paid') {
                $query->whereHas('course', fn ($q) => $q->where('is_free', false));
            }
        }

        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        return view('admin.enrollments.index', [
            'enrollments' => $query->paginate(15)->withQueryString(),
            'courses' => Course::orderBy('title')->get(['id', 'title']),
            'selectedCourse' => $request->get('course_id', 'all'),
            'selectedPriceFilter' => $request->get('price_filter', 'all'),
            'dateFrom' => $request->get('date_from', ''),
            'dateTo' => $request->get('date_to', ''),
            'search' => $request->get('search', ''),
            'stats' => [
                'total' => $this->trends->forModel(Enrollment::class, fn ($q) => $q),
                'students' => [
                    'count' => Enrollment::distinct()->count('user_id'),
                    'change' => 0,
                    'direction' => 'flat',
                ],
                'courses' => [
                    'count' => Enrollment::distinct()->count('course_id'),
                    'change' => 0,
                    'direction' => 'flat',
                ],
                'thisMonth' => $this->trends->forModel(Enrollment::class, fn ($q) => $q->whereBetween('created_at', [now()->startOfMonth(), now()])),
            ],
        ]);
    }

    public function show(Enrollment $enrollment): View
    {
        $enrollment->load(['user', 'course.category', 'course.subCategory', 'course.instructor']);

        return view('admin.enrollments.show', [
            'enrollment' => $enrollment,
            'otherEnrollments' => Enrollment::where('user_id', $enrollment->user_id)
                ->where('id', '!=', $enrollment->id)
                ->with('course.category')
                ->latest()
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.enrollments.form', [
            'enrollment' => new Enrollment(),
            'users' => User::where('role', '!=', 'admin')->orderBy('name')->get(['id', 'name', 'email']),
            'courses' => Course::where('status', 'active')->with(['category', 'subCategory', 'instructor'])->orderBy('title')->get([
                'id', 'user_id', 'category_id', 'sub_category_id', 'title', 'short_description',
                'thumbnail', 'price', 'discount_price', 'is_free', 'level', 'language', 'status',
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateEnrollment($request);

        if (Enrollment::where($validated)->exists()) {
            return back()->withInput()->with('flash_error', 'This student has already been enrolled in that course.');
        }

        return $this->tryAction(function () use ($validated) {
            Enrollment::create($validated);
        }, 'Student has been enrolled in the course.', 'admin.enrollments.index');
    }

    public function edit(Enrollment $enrollment): View
    {
        return view('admin.enrollments.form', [
            'enrollment' => $enrollment,
            'users' => User::where('role', '!=', 'admin')->orderBy('name')->get(['id', 'name', 'email']),
            'courses' => Course::where('status', 'active')->with(['category', 'subCategory', 'instructor'])->orderBy('title')->get([
                'id', 'user_id', 'category_id', 'sub_category_id', 'title', 'short_description',
                'thumbnail', 'price', 'discount_price', 'is_free', 'level', 'language', 'status',
            ]),
        ]);
    }

    public function update(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $validated = $this->validateEnrollment($request);

        if (Enrollment::where($validated)->where('id', '!=', $enrollment->id)->exists()) {
            return back()->withInput()->with('flash_error', 'This student has already been enrolled in that course.');
        }

        return $this->tryAction(function () use ($validated, $enrollment) {
            $enrollment->update($validated);
        }, 'Enrollment updated.', 'admin.enrollments.index');
    }

    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        return $this->tryActionBack(function () use ($enrollment) {
            $enrollment->delete();
        }, 'Enrollment removed.');
    }

    private function validateEnrollment(Request $request): array
    {
        return $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);
    }
}
