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
            'position'  => ['required', 'numeric', 'min:0'],
            'duration'  => ['required', 'numeric', 'min:1'],
            'seek_from' => ['nullable', 'numeric', 'min:0'],
            'seek_to'   => ['nullable', 'numeric', 'min:0'],
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

        /*
        * Documents are completed when opened.
        */
        if ($lesson->lesson_type === 'document') {
            DB::transaction(function () use (
                $viewer,
                $course,
                $lessonId
            ) {
                $progress = LessonProgress::query()
                    ->where('user_id', $viewer->id)
                    ->where('course_id', $course->id)
                    ->where('lesson_id', $lessonId)
                    ->lockForUpdate()
                    ->first() ?? new LessonProgress();

                if (!$progress->is_completed) {
                    $progress->user_id = $viewer->id;
                    $progress->course_id = $course->id;
                    $progress->lesson_id = $lessonId;
                    $progress->last_position_seconds = 1;
                    $progress->duration_seconds = 1;
                    $progress->last_reported_position_seconds = 1;
                    $progress->watched_ranges = [[0, 1]];
                    $progress->is_completed = true;
                    $progress->last_watched_at = now();
                    $progress->save();
                }
            });

            $certificate = $this->checkCertificate(
                (int) $viewer->id,
                (int) $course->id
            );

            return response()->json([
                'success' => true,
                'position' => 1,
                'duration' => 1,
                'completed' => true,
                'certificate_status' => $certificate['status'],
                'certificate_issued' => $certificate['status'] === 'issued',
                'certificate_message' => $certificate['message'],
                'certificate_url' => $certificate['url'],
            ]);
        }

        /*
        * Normalize video values.
        */
        $duration = (int) round((float) $data['duration']);
        $position = (int) round((float) $data['position']);

        abort_if(
            $duration < 1 || $position > $duration,
            422,
            'Invalid video position or duration.'
        );

        $seekFrom = isset($data['seek_from'])
            ? (int) round((float) $data['seek_from'])
            : null;

        $seekTo = isset($data['seek_to'])
            ? (int) round((float) $data['seek_to'])
            : null;

        abort_if(
            ($seekFrom !== null && $seekFrom > $duration) ||
            ($seekTo !== null && $seekTo > $duration),
            422,
            'Invalid seek position.'
        );

        /*
        * Only a forward seek adds a new skipped interval.
        * The frontend should send seek_from and seek_to only
        * when the video seeked event fires.
        */
        $isForwardSeek = (
            $seekFrom !== null &&
            $seekTo !== null &&
            $seekTo > $seekFrom &&
            abs($position - $seekTo) <= 2
        );
        $result = DB::transaction(function () use (
            $viewer,
            $course,
            $lessonId,
            $duration,
            $position,
            $seekFrom,
            $seekTo,
            $isForwardSeek
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

            $elapsed = $previousTime
                ? max(
                    0,
                    $now->getTimestamp() - $previousTime->getTimestamp()
                )
                : 0;

            $delta = $position - $previousPosition;

            /*
            * Count a forward seek interval.
            * Seeking backwards does not add new coverage.
            */
            if (
                !$alreadyCompleted &&
                $isForwardSeek
            ) {
                $ranges[] = [
                    $seekFrom,
                    $seekTo,
                ];
            }

            /*
            * Normal playback tracking:
            * credit plausible forward playback movement only.
            * Do not double-count a seek as ordinary playback.
            */
            $tooSoon = $previousTime && $elapsed < 1;

            if (
                !$alreadyCompleted &&
                !$isForwardSeek &&
                $previousTime &&
                !$tooSoon &&
                $elapsed <= 20 &&
                $delta > 0 &&
                // allow up to 2x playback speed plus a small tolerance
                $delta <= (($elapsed * 2) + 2)
            ) {
                $ranges[] = [
                    $previousPosition,
                    $position,
                ];
            }

            /*
            * Normalize ranges and clamp to the video duration.
            */
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

            /*
            * Sort ranges by start time.
            */
            usort(
                $normalized,
                fn ($a, $b) => $a[0] <=> $b[0]
            );

            /*
            * Merge overlapping ranges to avoid double-counting.
            */
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

            /*
            * Calculate unique covered seconds.
            */
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
            * Completion requires 95% unique coverage and coverage
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

            /*
            * Update the playback baseline.
            * An explicit seek must update the baseline even if it
            * occurs within the same server second.
            */
            if (!$tooSoon || $isForwardSeek) {
                $progress->last_reported_position_seconds = $position;
                $progress->last_watched_at = $now;
            }

            $progress->save();

            return [
                'newly_completed' => $completed && !$alreadyCompleted,
                'position' => $position,
                'duration' => $duration,
                'watched_seconds' => $watchedSeconds,
                'coverage_percent' => round($coverage * 100, 2),
                'completed' => $completed,
                'watched_ranges' => $merged,
            ];
        });

        /*
        * Check certificate eligibility after the lesson completes.
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
            (
                $newlyCompleted ||
                $position >= ($duration - 5)
            )
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
        }public function certificateStatus(
    Request $request,
    int $courseId
): JsonResponse {
    $viewer = $this->viewer($request);

    abort_unless($viewer && $viewer->id, 401);

    $course = Course::findOrFail($courseId);

    abort_unless($this->canAccess($course, $viewer), 403);

    $certificate = Certificate::query()
        ->where('user_id', $viewer->id)
        ->where('course_id', $course->id)
        ->first();

    return response()->json([
        'issued' => (bool) $certificate,
        'certificate_url' => $certificate
            ? route('user.certificates.show', $certificate->id)
            : null,
    ]);
}
    }