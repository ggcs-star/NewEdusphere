<?php

namespace App\Http\Controllers\Admin;

use App\Models\Lesson;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QuestionController extends BaseAdminController
{
    public function store(Request $request, Lesson $lesson): RedirectResponse
    {
        $validated = $this->validateQuestion($request);

        return $this->tryActionBack(function () use ($lesson, $validated) {
            $lesson->questions()->create([
                'title' => $validated['title'],
                'type' => $validated['type'],
                'options' => array_values($validated['options']),
                'correct_answers' => $validated['correct'],
                'order' => $lesson->questions()->max('order') + 1,
            ]);
        }, 'Question added.');
    }

    public function update(Request $request, Question $question): RedirectResponse
    {
        $validated = $this->validateQuestion($request);

        return $this->tryActionBack(function () use ($question, $validated) {
            $question->update([
                'title' => $validated['title'],
                'type' => $validated['type'],
                'options' => array_values($validated['options']),
                'correct_answers' => $validated['correct'],
            ]);
        }, 'Question updated.');
    }

    public function destroy(Question $question): RedirectResponse
    {
        return $this->tryActionBack(function () use ($question) {
            $question->delete();
        }, 'Question removed.');
    }

    private function validateQuestion(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'type' => ['required', 'in:single_choice,multiple_choice'],
            'options' => ['required', 'array', 'min:2'],
            'options.*' => ['required', 'string', 'max:255'],
            'correct' => ['required', 'array', 'min:1'],
        ]);
    }
}
