<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Course;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Course listing/detail/access logic shared by the web (Blade) course
 * controller and the Api\V1 course controller, so both surfaces stay
 * in sync instead of maintaining two copies of the same rules.
 */
class CourseCatalogService
{
    /**
     * Active courses matching the given filters (q, category, price).
     */
    public function listCourses(array $filters = []): LengthAwarePaginator
    {
        $query = Course::with(['instructor', 'category'])
            ->withCount(['sections', 'lessons', 'enrollments'])
            ->where('status', 'active');

        if ($search = trim((string) ($filters['q'] ?? ''))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        if ($category = (int) ($filters['category'] ?? 0)) {
            $query->where(function ($builder) use ($category) {
                $builder->where('category_id', $category)
                    ->orWhere('sub_category_id', $category);
            });
        }

        if (($filters['price'] ?? null) === 'free') {
            $query->where('is_free', true);
        }

        if (($filters['price'] ?? null) === 'paid') {
            $query->where('is_free', false);
        }

        return $query->latest()
            ->paginate((int) ($filters['per_page'] ?? 12))
            ->withQueryString();
    }

    public function activeCategories(): Collection
    {
        return Category::where('status', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Full detail payload for a course: the course itself, related
     * courses, enrollment/unlock state for the viewer, which lesson
     * ids are free previews, and (if enrolled) the quiz score summary.
     */
    public function courseDetail(string $slug, int $id, ?User $viewer): array
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

        abort_if($slug !== $course->slug, 404);

        $relatedCourses = Course::with(['instructor', 'category'])
            ->where('status', 'active')
            ->where('category_id', $course->category_id)
            ->whereKeyNot($course->id)
            ->latest()
            ->take(4)
            ->get();

        $isEnrolled = $this->hasCourseAccess($viewer, $course);
        $isCourseUnlocked = (bool) $course->is_free || $isEnrolled;

        $sections = $course->sections
            ->sortBy(fn ($section) => (int) $section->order)
            ->values();

        $allLessons = $sections->flatMap(function ($section) {
            return $section->lessons
                ->sortBy(fn ($lesson) => (int) $lesson->order)
                ->values();
        })->values();

        $previewLessonIds = $allLessons
            ->take(2)
            ->pluck('id')
            ->map(fn ($lessonId) => (int) $lessonId)
            ->all();

        $quizScoreSummary = $isEnrolled
            ? $this->quizScoreSummary($viewer, $allLessons)
            : null;

        return [
            'course' => $course,
            'sections' => $sections,
            'relatedCourses' => $relatedCourses,
            'isEnrolled' => $isEnrolled,
            'isCourseUnlocked' => $isCourseUnlocked,
            'previewLessonIds' => $previewLessonIds,
            'quizScoreSummary' => $quizScoreSummary,
        ];
    }

    /**
     * Resolve a single lesson plus whether the viewer is allowed to see
     * its real content (unlocked course, or one of the free previews).
     */
    public function lessonAccess(string $slug, int $lessonId, ?User $viewer): array
    {
        $course = Course::with(['instructor', 'category', 'sections.lessons'])
            ->where('status', 'active')
            ->where('slug', $slug)
            ->firstOrFail();

        $sections = $course->sections
            ->sortBy(fn ($section) => (int) $section->order)
            ->values();

        $allLessons = $sections->flatMap(function ($section) {
            return $section->lessons
                ->sortBy(fn ($item) => (int) $item->order)
                ->values();
        })->values();

        $currentLesson = $allLessons->firstWhere('id', $lessonId);

        abort_if(! $currentLesson, 404);

        $isEnrolled = $this->hasCourseAccess($viewer, $course);
        $isCourseUnlocked = (bool) $course->is_free || $isEnrolled;

        $previewLessonIds = $allLessons
            ->take(2)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $isPreviewLesson = in_array((int) $currentLesson->id, $previewLessonIds, true);

        abort_unless(
            $isCourseUnlocked || $isPreviewLesson,
            403,
            'Please purchase this course to access this lesson.'
        );

        return [
            'course' => $course,
            'lesson' => $currentLesson,
            'isEnrolled' => $isEnrolled,
            'isPreviewLesson' => $isPreviewLesson,
        ];
    }

    /**
     * Whether the viewer has a paid/free enrollment record for the course.
     * Enrollment rows only ever get created after successful payment
     * verification (or immediately for free courses) — see CheckoutService.
     */
    public function hasCourseAccess(?User $viewer, Course $course): bool
    {
        if (! $viewer || empty($viewer->id)) {
            return false;
        }

        return DB::table('enrollments')
            ->where('user_id', $viewer->id)
            ->where('course_id', $course->id)
            ->exists();
    }

    /**
     * Per-quiz / total score breakdown for an enrolled viewer, based on
     * their saved quiz attempts.
     */
    private function quizScoreSummary(?User $viewer, $allLessons): ?array
    {
        if (! $viewer || empty($viewer->id)) {
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
}
