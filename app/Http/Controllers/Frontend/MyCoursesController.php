<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use App\Models\Certificate;
use App\Services\CertificateIssuanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

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

        /*
         * Fetch certificates for this user and the courses
         * shown on the current page.
         */
        $courseIds = $courses->getCollection()->pluck('id');

        $certificates = Certificate::query()
            ->where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->get()
            ->keyBy('course_id');

        /*
         * Self-heal for students who finished before the certificate fix:
         * for courses on this page that have no certificate yet, re-run the
         * eligibility check. The service verifies enrollment, lessons,
         * quizzes and template, and never duplicates a certificate.
         * Failures are logged and never break the page.
         * (Safe to remove once old students have been backfilled.)
         */
        $missing = $courseIds->diff($certificates->keys());

        if ($missing->isNotEmpty()) {
            $service = app(CertificateIssuanceService::class);

            foreach ($missing as $missingCourseId) {
                try {
                    $service->issueIfEligible(
                        (int) $user->id,
                        (int) $missingCourseId
                    );
                } catch (Throwable $e) {
                    Log::error('Certificate re-check failed on My Courses page', [
                        'user_id' => $user->id,
                        'course_id' => $missingCourseId,
                        'exception' => $e,
                    ]);
                }
            }

            $certificates = Certificate::query()
                ->where('user_id', $user->id)
                ->whereIn('course_id', $courseIds)
                ->get()
                ->keyBy('course_id');
        }

        // Attach the certificate ID to its matching course.
        $courses->getCollection()->each(function ($course) use ($certificates) {
            $certificate = $certificates->get($course->id);

            $course->certificate_id = $certificate?->id;
        });

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
