<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
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

        return view('admin.enrollments.index', [
            'enrollments' => $query->paginate(15)->withQueryString(),
            'courses' => Course::orderBy('title')->get(['id', 'title']),
            'selectedCourse' => $request->get('course_id', 'all'),
            'search' => $request->get('search', ''),
        ]);
    }
}
