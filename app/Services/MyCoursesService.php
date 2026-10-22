<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * "My Courses" / dashboard logic shared by Frontend\MyCoursesController,
 * the web "/dashboard" route and the Api\V1 equivalents.
 */
class MyCoursesService
{
    public function __construct(private readonly CertificateIssuanceService $certificates)
    {
    }

    /**
     * Paginated list of the user's enrolled (active) courses, each
     * annotated with certificate_id (self-healing missing certificates
     * for students who finished before the certificate issuance fix).
     */
    public function enrolledCourses(User $user, int $perPage = 9): LengthAwarePaginator
    {
        $courses = Course::query()
            ->with([
                'instructor',
                'category',
                'enrollments' => fn ($query) => $query->where('user_id', $user->id),
                'payments' => fn ($query) => $query->where('user_id', $user->id)->latest('id'),
            ])
            ->withCount(['sections', 'lessons'])
            ->where('status', 'active')
            ->whereHas('enrollments', fn ($query) => $query->where('user_id', $user->id))
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString();

        $courseIds = $courses->getCollection()->pluck('id');

        $certificates = $this->certificatesFor($user, $courseIds);

        $missing = $courseIds->diff($certificates->keys());

        if ($missing->isNotEmpty()) {
            foreach ($missing as $missingCourseId) {
                try {
                    $this->certificates->issueIfEligible((int) $user->id, (int) $missingCourseId);
                } catch (Throwable $e) {
                    Log::error('Certificate re-check failed on My Courses page', [
                        'user_id' => $user->id,
                        'course_id' => $missingCourseId,
                        'exception' => $e,
                    ]);
                }
            }

            $certificates = $this->certificatesFor($user, $courseIds);
        }

        $courses->getCollection()->each(function ($course) use ($certificates) {
            $course->certificate_id = $certificates->get($course->id)?->id;
        });

        return $courses;
    }

    /**
     * @return array{total_enrollments: int, total_spent: float, successful_payments: int}
     */
    public function summary(User $user): array
    {
        return [
            'total_enrollments' => Enrollment::query()->where('user_id', $user->id)->count(),
            'total_spent' => (float) Payment::query()
                ->where('user_id', $user->id)
                ->where('status', 'paid')
                ->sum('amount'),
            'successful_payments' => Payment::query()
                ->where('user_id', $user->id)
                ->where('status', 'paid')
                ->count(),
        ];
    }

    /**
     * Recent enrolled courses + total count, for the dashboard summary.
     *
     * @return array{courses: Collection, enrolled_count: int}
     */
    public function dashboardSummary(User $user, int $limit = 6): array
    {
        $enrolledIds = DB::table('enrollments')->where('user_id', $user->id)->pluck('course_id');

        $courses = $enrolledIds->isNotEmpty()
            ? Course::with(['instructor', 'category'])
                ->whereIn('id', $enrolledIds)
                ->latest()
                ->take($limit)
                ->get()
            : collect();

        return [
            'courses' => $courses,
            'enrolled_count' => $enrolledIds->count(),
        ];
    }

    /**
     * All of the user's payment records, newest first — for the
     * "Purchase History" page.
     */
    public function purchaseHistory(User $user): Collection
    {
        return Payment::with('course')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get();
    }

    private function certificatesFor(User $user, Collection $courseIds): Collection
    {
        return Certificate::query()
            ->where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->get()
            ->keyBy('course_id');
    }
}
