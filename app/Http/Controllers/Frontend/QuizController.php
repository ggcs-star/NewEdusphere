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

        $hasAccess = (bool) $course->is_free
            || DB::table('enrollments')
                ->where('user_id', $viewer->id)
                ->where('course_id', $course->id)
                ->exists();

        // Keep the existing rule: the first two course lessons
        // are available as free previews.
        if (!$hasAccess) {
            $previewIds = $course->sections
                ->sortBy(fn ($section) => (int) $section->order)
                ->flatMap(function ($section) {
                    return $section->lessons
                        ->sortBy(fn ($item) => (int) $item->order);
                })
                ->take(2)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $hasAccess = in_array(
                (int) $lesson->id,
                $previewIds,
                true
            );
        }

        abort_unless($hasAccess, 403, 'You do not have access to this course.');

        abort_unless(
            $lesson->lesson_type === 'quiz',
            422,
            'This lesson is not a quiz.'
        );

        return [$course, $lesson];
    }

    /**
     * Return quiz questions without correct answers.
     * If the quiz was already submitted, return its saved result.
     */
    public function show(Request $request, int $courseId, int $lessonId): JsonResponse
    {
        $viewer = $this->viewer();

        [, $lesson] = $this->accessibleLesson(
            $courseId,
            $lessonId,
            $viewer
        );

        $attempt = QuizAttempt::where('user_id', $viewer->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        // A fresh attempt is only handed out when the admin allowed retries
        // and the student explicitly asked to retake the quiz.
        $wantsRetry = $attempt && $lesson->allow_retry && $request->boolean('retry');

        if ($attempt && !$wantsRetry) {
            return response()->json([
                'submitted' => true,
                'allow_retry' => (bool) $lesson->allow_retry,
                'result' => $this->resultData($attempt, $lesson),
                'retry_url' => $lesson->allow_retry
                    ? route('courses.quizzes.show', [$courseId, $lessonId]) . '?retry=1'
                    : null,
            ]);
        }

        $questions = $lesson->questions()
            ->orderBy('order')
            ->get()
            ->map(function (Question $question) {
                $options = $question->options ?? [];

                return [
                    'id' => $question->id,
                    'title' => $question->title,
                    'type' => $question->type,
                    'points' => (int) $question->points,
                    'options' => array_values($options),
                ];
            })
            ->values();

        return response()->json([
            'submitted' => false,
            'allow_retry' => (bool) $lesson->allow_retry,
            'questions' => $questions,
            'total_score' => (int) $lesson->questions()->sum('points'),
            'submit_url' => route('courses.quizzes.submit', [
                $courseId,
                $lessonId,
            ]),
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
        $viewer = $this->viewer();

        [, $lesson] = $this->accessibleLesson(
            $courseId,
            $lessonId,
            $viewer
        );

        $existing = QuizAttempt::where('user_id', $viewer->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($existing && !$lesson->allow_retry) {
            return response()->json([
                'message' => 'This quiz has already been submitted.',
                'submitted' => true,
                'allow_retry' => false,
                'result' => $this->resultData($existing, $lesson),
            ], 409);
        }

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['present'],
            'answers.*' => ['nullable'],
        ]);

        $questions = $lesson->questions()
            ->orderBy('order')
            ->get();

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

        $attributes = [
            'score' => $score,
            'total_score' => $totalScore,
            'answers' => $savedAnswers,
            'submitted_at' => now(),
        ];

        try {
            if ($existing) {
                // Retry: overwrite the previous attempt with the new one.
                $existing->update($attributes);
                $attempt = $existing;
            } else {
                $attempt = QuizAttempt::create([
                    'user_id' => $viewer->id,
                    'lesson_id' => $lesson->id,
                    ...$attributes,
                ]);
            }
        } catch (QueryException $exception) {
            // The database unique constraint handles simultaneous first-time submissions.
            $attempt = QuizAttempt::where('user_id', $viewer->id)
                ->where('lesson_id', $lesson->id)
                ->first();

            if (!$attempt) {
                throw $exception;
            }

            if (!$lesson->allow_retry) {
                return response()->json([
                    'message' => 'This quiz has already been submitted.',
                    'submitted' => true,
                    'allow_retry' => false,
                    'result' => $this->resultData($attempt, $lesson),
                ], 409);
            }

            $attempt->update($attributes);
        }

        return response()->json([
            'message' => 'Quiz submitted successfully.',
            'submitted' => true,
            'allow_retry' => (bool) $lesson->allow_retry,
            'result' => $this->resultData($attempt, $lesson),
        ]);
    }

    private function resultData(QuizAttempt $attempt, Lesson $lesson): array
    {
        $answers = $attempt->answers ?? [];

        $review = $lesson->questions()
            ->orderBy('order')
            ->get()
            ->map(function (Question $question) use ($answers) {
                $options = array_values($question->options ?? []);

                $correct = array_values(array_unique(array_map(
                    'intval',
                    $question->correct_answers ?? []
                )));
                sort($correct);

                $given = $answers[(string) $question->id] ?? null;

                if ($question->type === 'multiple_choice') {
                    $givenAnswer = is_array($given)
                        ? array_values(array_unique(array_map('intval', $given)))
                        : [];
                    $givenSorted = $givenAnswer;
                    sort($givenSorted);
                    $isCorrect = $givenSorted === $correct;
                } else {
                    $givenAnswer = $given === null ? null : (int) $given;
                    $isCorrect = $givenAnswer !== null && in_array($givenAnswer, $correct, true);
                }

                return [
                    'id' => $question->id,
                    'title' => $question->title,
                    'type' => $question->type,
                    'points' => (int) $question->points,
                    'options' => $options,
                    'correct_answers' => $correct,
                    'given_answer' => $givenAnswer,
                    'is_correct' => $isCorrect,
                ];
            })
            ->values();

        return [
            'score' => (int) $attempt->score,
            'total_score' => (int) $attempt->total_score,
            'percentage' => $attempt->total_score > 0
                ? round($attempt->score / $attempt->total_score * 100, 2)
                : 0,
            'submitted_at' => $attempt->submitted_at?->toIso8601String(),
            'review' => $review,
        ];
    }
}