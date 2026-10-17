<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Course;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\CertificateIssuanceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Throwable;

class LessonProgressController extends Controller
{
    /**
     * Get the currently logged-in user.
     */
    private function viewer(Request $request)
    {
        $sessionUser = $request->session()->get('user');

        $userId = is_object($sessionUser)
            ? ($sessionUser->id ?? null)
            : (is_array($sessionUser)
                ? ($sessionUser['id'] ?? null)
                : null);

        if ($request->session()->has('user_token') && $userId) {
            return User::query()->find($userId);
        }

        return function_exists('current_admin_user')
            ? current_admin_user()
            : null;
    }

    /**
     * Check whether the user can access the course.
     */
    private function canAccess(Course $course, $viewer): bool
    {
        if (!$viewer || empty($viewer->id)) {
            return false;
        }

        if ($course->is_free) {
            return true;
        }

        return DB::table('enrollments')
            ->where('user_id', $viewer->id)
            ->where('course_id', $course->id)
            ->exists();
    }

    /**
     * Load saved progress for a lesson.
     */
    public function show(
        Request $request,
        int $courseId,
        int $lessonId
    ): JsonResponse {
        $viewer = $this->viewer($request);

        abort_unless($viewer && $viewer->id, 401);

        $course = Course::with('sections.lessons')
            ->whereKey($courseId)
            ->firstOrFail();

        abort_unless(
            $this->canAccess($course, $viewer),
            403
        );

        $lesson = $course->sections
            ->flatMap(fn ($section) => $section->lessons)
            ->firstWhere('id', $lessonId);

        abort_unless($lesson, 404);

        $progress = LessonProgress::query()
            ->where('user_id', $viewer->id)
            ->where('course_id', $course->id)
            ->where('lesson_id', $lessonId)
            ->first();

        return response()->json([
            'position' => (int) (
                $progress?->last_position_seconds ?? 0
            ),
            'duration' => (int) (
                $progress?->duration_seconds ?? 0
            ),
            'completed' => (bool) (
                $progress?->is_completed ?? false
            ),
            'watched_ranges' => $progress?->watched_ranges ?? [],
        ]);
    }

    /**
     * Save playback progress.
     *
     * Completion is calculated on the server using watched intervals.
     * A client-supplied completed flag is deliberately not accepted.
     */
    public function save(
        Request $request,
        int $courseId,
        int $lessonId
    ): JsonResponse {
        $data = $request->validate([
            'position' => ['required', 'numeric', 'min:0'],
            'duration' => ['required', 'numeric', 'min:1'],
        ]);

        $viewer = $this->viewer($request);

        abort_unless($viewer && $viewer->id, 401);

        $course = Course::with('sections.lessons')
            ->whereKey($courseId)
            ->firstOrFail();

        abort_unless(
            $this->canAccess($course, $viewer),
            403
        );

        $lesson = $course->sections
            ->flatMap(fn ($section) => $section->lessons)
            ->firstWhere('id', $lessonId);

        abort_unless($lesson, 404);

        abort_unless(
            $lesson->lesson_type !== 'quiz',
            422,
            'Quiz completion must use the quiz endpoint.'
        );

        $duration = (int) round((float) $data['duration']);
        $position = (int) round((float) $data['position']);

        abort_if(
            $duration < 1 || $position > $duration,
            422,
            'Invalid video position or duration.'
        );

        /*
         * Lock the existing progress row to reduce concurrent
         * update conflicts for the same user and lesson.
         */
        $result = DB::transaction(function () use (
            $viewer,
            $course,
            $lessonId,
            $duration,
            $position
        ) {
            $progress = LessonProgress::query()
                ->where('user_id', $viewer->id)
                ->where('course_id', $course->id)
                ->where('lesson_id', $lessonId)
                ->lockForUpdate()
                ->first();

            if (!$progress) {
                $progress = new LessonProgress();

                $progress->user_id = $viewer->id;
                $progress->course_id = $course->id;
                $progress->lesson_id = $lessonId;
                $progress->last_position_seconds = 0;
                $progress->duration_seconds = $duration;
                $progress->last_reported_position_seconds = 0;
                $progress->watched_ranges = [];
                $progress->is_completed = false;
            }

            $alreadyCompleted = (bool) $progress->is_completed;

            $ranges = $progress->watched_ranges ?? [];

            if (!is_array($ranges)) {
                $ranges = [];
            }

            $now = now();

            $previousPosition = (int) (
                $progress->last_reported_position_seconds ?? 0
            );

            $previousTime = $progress->last_watched_at;

            // Whole seconds between the previous accepted report and now.
            $elapsed = $previousTime
                ? max(0, $now->getTimestamp() - $previousTime->getTimestamp())
                : 0;

            $delta = $position - $previousPosition;

            /*
             * HARDENING: a report that lands in the same server second is
             * ignored for crediting AND the baseline is left untouched, so
             * the next report credits the whole span. Credit is still
             * limited by real elapsed time, so spamming requests cannot
             * fast-forward progress.
             */
            $tooSoon = $previousTime && $elapsed < 1;

            /*
             * Count a forward interval only when its movement is plausible
             * for the elapsed server time (+2s tolerance for rounding).
             * Long gaps and large forward seeks do not add coverage.
             */
            if (
                !$alreadyCompleted &&
                $previousTime &&
                !$tooSoon &&
                $elapsed <= 20 &&
                $delta > 0 &&
                $delta <= ($elapsed + 2)
            ) {
                $ranges[] = [
                    $previousPosition,
                    $position,
                ];
            }

            // Normalize intervals and clamp them to the current duration.
            $normalized = [];

            foreach ($ranges as $range) {
                if (!is_array($range) || count($range) < 2) {
                    continue;
                }

                $start = max(
                    0,
                    min((int) $range[0], $duration)
                );

                $end = max(
                    0,
                    min((int) $range[1], $duration)
                );

                if ($end > $start) {
                    $normalized[] = [$start, $end];
                }
            }

            // Sort intervals by their start position.
            usort(
                $normalized,
                fn ($a, $b) => $a[0] <=> $b[0]
            );

            // Merge overlapping intervals to prevent double-counting.
            $merged = [];

            foreach ($normalized as $range) {
                $lastIndex = count($merged) - 1;

                if (
                    $lastIndex >= 0 &&
                    $range[0] <= $merged[$lastIndex][1]
                ) {
                    $merged[$lastIndex][1] = max(
                        $merged[$lastIndex][1],
                        $range[1]
                    );
                } else {
                    $merged[] = $range;
                }
            }

            $watchedSeconds = 0;
            $furthestWatched = 0;

            foreach ($merged as $range) {
                $watchedSeconds += $range[1] - $range[0];

                $furthestWatched = max(
                    $furthestWatched,
                    $range[1]
                );
            }

            $coverage = $duration > 0
                ? $watchedSeconds / $duration
                : 0;

            /*
             * Require at least 95% unique coverage and playback
             * reaching the final five seconds.
             */
            $completed = $alreadyCompleted || (
                $coverage >= 0.95 &&
                $furthestWatched >= ($duration - 5)
            );

            $progress->course_id = $course->id;
            $progress->duration_seconds = $duration;
            $progress->last_position_seconds = $position;
            $progress->watched_ranges = $merged;
            $progress->is_completed = $completed;

            // Move the credit baseline only when this report was evaluated.
            if (!$tooSoon) {
                $progress->last_reported_position_seconds = $position;
                $progress->last_watched_at = $now;
            }

            $progress->save();

            return [
                'newly_completed' => $completed && !$alreadyCompleted,
                'position' => $position,
                'duration' => $duration,
                'watched_seconds' => $watchedSeconds,
                'coverage_percent' => round(
                    $coverage * 100,
                    2
                ),
                'completed' => $completed,
                'watched_ranges' => $merged,
            ];
        });

        /*
         * Check certificate eligibility when this request completed the
         * lesson, or when an already-completed lesson is replayed to its end.
         * Not on every 1-second tick, to avoid hammering the service.
         */
        $newlyCompleted = (bool) $result['newly_completed'];
        unset($result['newly_completed']);

        $certificate = [
            'status' => 'not_checked',
            'message' => null,
            'url' => null,
        ];

        if (
            $result['completed'] &&
            ($newlyCompleted || $position >= ($duration - 5))
        ) {
            $certificate = $this->checkCertificate(
                (int) $viewer->id,
                (int) $course->id
            );
        }

        return response()->json([
            'success' => true,
            ...$result,
            'certificate_status' => $certificate['status'],
            'certificate_issued' => $certificate['status'] === 'issued',
            'certificate_message' => $certificate['message'],
            'certificate_url' => $certificate['url'],
        ]);
    }

    /**
     * Run the issuance service and report exactly what happened:
     * issued | already_issued | incomplete | error.
     * Failures are logged, never swallowed.
     */
    private function checkCertificate(int $userId, int $courseId): array
    {
        try {
            $existedBefore = Certificate::query()
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->exists();

            app(CertificateIssuanceService::class)
                ->issueIfEligible($userId, $courseId);

            // Re-read from the DB so the result does not depend on the
            // service's return value.
            $certificate = Certificate::query()
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->latest('id')
                ->first();

            if ($certificate === null) {
                return [
                    'status' => 'incomplete',
                    'message' => 'Certificate not issued yet: finish all lessons and submit every quiz (or the course has no active certificate template).',
                    'url' => null,
                ];
            }

            $url = Route::has('user.certificates.show')
                ? route('user.certificates.show', $certificate->id)
                : null;

            return $existedBefore
                ? ['status' => 'already_issued', 'message' => 'Certificate already issued.', 'url' => $url]
                : ['status' => 'issued', 'message' => 'Certificate issued.', 'url' => $url];
        } catch (Throwable $e) {
            Log::error('Certificate issuance failed after lesson completion', [
                'user_id' => $userId,
                'course_id' => $courseId,
                'exception' => $e,
            ]);

            return ['status' => 'error', 'message' => 'Certificate could not be issued. Please contact support.', 'url' => null];
        }
    }
}