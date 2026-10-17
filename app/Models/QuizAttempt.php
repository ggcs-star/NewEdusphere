<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    protected $table = 'quiz_attempts';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'score',
        'total_score',
        'answers',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'integer',
        'total_score' => 'integer',
        'submitted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Per-question breakdown of this attempt: each question's options,
     * the correct answer(s), what the student picked, and whether it was
     * right. Safe to reveal only once an attempt has been submitted.
     */
    public function review(): array
    {
        $answers = $this->answers ?? [];

        return $this->lesson->questions()
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
            ->values()
            ->all();
    }
}
