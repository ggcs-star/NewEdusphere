<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuizAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class QuizController extends Controller
{
    private function viewer()
    {
        return function_exists('current_admin_user')
            ? current_admin_user()
            : null;
    }

    /**
     * Confirm that the lesson belongs to the course and that
     * the current user can access the lesson.
     */
    private function accessibleLesson(
        int $courseId,
        int $lessonId,
        $viewer
    ): array {
        abort_unless($viewer && $viewer->id, 401, 'Please log in first.');

        $course = Course::with('sections.lessons')
            ->whereKey($courseId)
            ->firstOrFail();

        $lesson = $course->sections
            ->flatMap(fn ($section) => $section->lessons)
            ->firstWhere('id', $lessonId);

        abort_unless($lesson, 404, 'Lesson not found.');

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
     * Accept all answers together and score them on the server.
     */
    public function submit(
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

        abort_if($questions->isEmpty(), 422, 'This quiz has no questions.');

        $questionIds = $questions->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $submittedAnswers = $validated['answers'];

        // Do not accept IDs for questions outside this quiz.
        $extraIds = array_diff(
            array_map('strval', array_keys($submittedAnswers)),
            $questionIds
        );

        abort_if(
            !empty($extraIds),
            422,
            'Invalid question submitted.'
        );

        $score = 0;
        $savedAnswers = [];

        foreach ($questions as $question) {
            $questionId = (string) $question->id;
            $options = array_values($question->options ?? []);
            $rawAnswer = $submittedAnswers[$questionId] ?? null;

            if ($question->type === 'multiple_choice') {
                abort_if(
                    $rawAnswer !== null && !is_array($rawAnswer),
                    422,
                    'Invalid answer format.'
                );

                $selected = array_values(array_unique(array_map(
                    'intval',
                    $rawAnswer ?? []
                )));

                foreach ($selected as $index) {
                    abort_if(
                        $index < 0 || $index >= count($options),
                        422,
                        'Invalid option selected.'
                    );
                }

                sort($selected);

                $correct = array_values(array_unique(array_map(
                    'intval',
                    $question->correct_answers ?? []
                )));
                sort($correct);

                $isCorrect = $selected === $correct;

                $savedAnswers[$questionId] = $selected;
            } else {
                abort_if(
                    $rawAnswer !== null && !is_scalar($rawAnswer),
                    422,
                    'Invalid answer format.'
                );

                $selected = $rawAnswer === null || $rawAnswer === ''
                    ? null
                    : (int) $rawAnswer;

                if ($selected !== null) {
                    abort_if(
                        $selected < 0 || $selected >= count($options),
                        422,
                        'Invalid option selected.'
                    );
                }

                $correct = array_map(
                    'intval',
                    $question->correct_answers ?? []
                );

                $isCorrect = $selected !== null
                    && in_array($selected, $correct, true);

                $savedAnswers[$questionId] = $selected;
            }

            if ($isCorrect) {
                $score += (int) $question->points;
            }
        }

        $totalScore = (int) $questions->sum('points');

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
