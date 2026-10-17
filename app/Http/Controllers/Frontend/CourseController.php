<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CourseController extends Controller
{
    /**
     * Display all active courses.
     */
    public function index(Request $request): View
    {
        $query = Course::with(['instructor', 'category'])
            ->withCount(['sections', 'lessons', 'enrollments'])
            ->where('status', 'active');

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere(
                        'short_description',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if ($category = $request->integer('category')) {
            $query->where(function ($builder) use ($category) {
                $builder->where('category_id', $category)
                    ->orWhere('sub_category_id', $category);
            });
        }

        if ($request->query('price') === 'free') {
            $query->where('is_free', true);
        }

        if ($request->query('price') === 'paid') {
            $query->where('is_free', false);
        }

        return view('frontend.courses.index', [
            'courses' => $query->latest()
                ->paginate(12)
                ->withQueryString(),

            'categories' => Category::where('status', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Display course details with free-preview and purchase status.
     *
     * First two lessons across the entire course are free previews.
     * Remaining lessons require course access.
     */
    public function show(string $slug, int $id): View
    {
        $course = Course::with([
            'instructor',
            'category',
            'subCategory',
            'sections.lessons.questions',
        ])
            ->withCount('enrollments')
            ->where('status', 'active')
            ->where('id', $id)
            ->firstOrFail();

        // Keep existing URL compatibility and slug validation.
        abort_if($slug !== $course->slug, 404);

        $relatedCourses = Course::with(['instructor', 'category'])
            ->where('status', 'active')
            ->where('category_id', $course->category_id)
            ->whereKeyNot($course->id)
            ->latest()
            ->take(4)
            ->get();

        $viewer = function_exists('current_admin_user')
            ? current_admin_user()
            : null;

        $isEnrolled = $this->hasCourseAccess($viewer, $course);

        // Free courses and purchased courses unlock every lesson.
        $isCourseUnlocked = (bool) $course->is_free || $isEnrolled;

        // Sort sections and lessons in their configured order.
        $sections = $course->sections
            ->sortBy(fn ($section) => (int) $section->order)
            ->values();

        // Flatten lessons across all sections.
        $allLessons = $sections->flatMap(function ($section) {
            return $section->lessons
                ->sortBy(fn ($lesson) => (int) $lesson->order)
                ->values();
        })->values();

        // First two lessons across the whole course are previews.
        $previewLessonIds = $allLessons
            ->take(2)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $quizScoreSummary = $isEnrolled
            ? $this->quizScoreSummary($viewer, $allLessons)
            : null;

        return view('frontend.courses.show', [
            'course' => $course,
            'relatedCourses' => $relatedCourses,
            'isEnrolled' => $isEnrolled,
            'isCourseUnlocked' => $isCourseUnlocked,
            'previewLessonIds' => $previewLessonIds,
            'quizScoreSummary' => $quizScoreSummary,
        ]);
    }

    /**
     * Build the per-quiz / total score breakdown shown in the course
     * sidebar for enrolled students, based on their saved quiz attempts.
     */
    private function quizScoreSummary($viewer, $allLessons): ?array
    {
        if (!$viewer || empty($viewer->id)) {
            return null;
        }

        $quizLessons = $allLessons->where('lesson_type', 'quiz')->values();

        if ($quizLessons->isEmpty()) {
            return null;
        }

        $attempts = QuizAttempt::where('user_id', $viewer->id)
            ->whereIn('lesson_id', $quizLessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        $quizzes = $quizLessons->map(function ($lesson) use ($attempts) {
            $attempt = $attempts->get($lesson->id);

            return [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'total_score' => (int) $lesson->total_score,
                'score' => $attempt ? (int) $attempt->score : null,
                'attempted' => (bool) $attempt,
            ];
        })->values()->all();

        return [
            'quizzes' => $quizzes,
            'total_possible' => (int) $quizLessons->sum('total_score'),
            'total_achieved' => (int) $attempts->sum('score'),
            'attempted_count' => $attempts->count(),
        ];
    }

    /**
     * Display a lesson only if it is free preview or the viewer
     * has access to the course.
     */
    public function learn(string $slug, int $lesson): View
    {
        $course = Course::with([
            'instructor',
            'category',
            'sections.lessons',
        ])
            ->where('status', 'active')
            ->where('slug', $slug)
            ->firstOrFail();

        // Sort sections in configured order.
        $sections = $course->sections
            ->sortBy(fn ($section) => (int) $section->order)
            ->values();

        // Sort lessons within each section and flatten the collection.
        $allLessons = $sections->flatMap(function ($section) {
            return $section->lessons
                ->sortBy(fn ($item) => (int) $item->order)
                ->values();
        })->values();

        $currentLesson = $allLessons->firstWhere('id', $lesson);

        abort_if(!$currentLesson, 404);

        $viewer = function_exists('current_admin_user')
            ? current_admin_user()
            : null;

        $isEnrolled = $this->hasCourseAccess($viewer, $course);

        // A free course or a purchased course unlocks every lesson.
        $isCourseUnlocked = (bool) $course->is_free || $isEnrolled;

        // First two lessons are free previews, regardless of section.
        $previewLessonIds = $allLessons
            ->take(2)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $isPreviewLesson = in_array(
            (int) $currentLesson->id,
            $previewLessonIds,
            true
        );

        // Block direct URL access to paid lessons without access.
        abort_unless(
            $isCourseUnlocked || $isPreviewLesson,
            403,
            'Please purchase this course to access this lesson.'
        );

        return view('frontend.courses.learn', [
            'course' => $course,
            'currentLesson' => $currentLesson,
            'isEnrolled' => $isEnrolled,
            'isPreviewLesson' => $isPreviewLesson,
        ]);
    }

    /**
     * Check whether the current viewer has an enrollment record.
     *
     * Enrollment records must only be created after successful
     * payment verification for paid courses.
     */
    private function hasCourseAccess($viewer, Course $course): bool
    {
        if (!$viewer || empty($viewer->id)) {
            return false;
        }

        return DB::table('enrollments')
            ->where('user_id', $viewer->id)
            ->where('course_id', $course->id)
            ->exists();
    }

    /**
     * Display course categories.
     */
    public function categories(): View
    {
        $categories = Category::where('status', true)
            ->withCount('children')
            ->orderBy('name')
            ->paginate(18);

        return view(
            'frontend.categories.index',
            compact('categories')
        );
    }
}
