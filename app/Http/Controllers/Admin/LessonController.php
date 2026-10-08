<?php

namespace App\Http\Controllers\Admin;

use App\Models\Section;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class LessonController extends BaseAdminController
{
    public function store(Request $request, Section $section): RedirectResponse
    {
        $validated = $this->validateLesson($request);

        return $this->tryActionBack(function () use ($section, $validated) {
            $section->lessons()->create([
                ...$validated,
                'course_id' => $section->course_id,
                'order' => $section->lessons()->max('order') + 1,
            ]);
        }, 'Lesson added.');
    }

    public function update(Request $request, Lesson $lesson): RedirectResponse
    {
        $validated = $this->validateLesson($request);

        return $this->tryActionBack(function () use ($lesson, $validated) {
            $lesson->update($validated);
        }, 'Lesson updated.');
    }

    public function destroy(Lesson $lesson): RedirectResponse
    {
        return $this->tryActionBack(function () use ($lesson) {
            $lesson->delete();
        }, 'Lesson removed.');
    }

    public function reorder(Request $request, Section $section): JsonResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        try {
            foreach ($validated['order'] as $index => $lessonId) {
                Lesson::where('id', $lessonId)
                    ->where('section_id', $section->id)
                    ->update(['order' => $index]);
            }
        } catch (Throwable $e) {
            report($e);

            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }

    private function validateLesson(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'lesson_type' => ['required', 'in:video,document,quiz'],
            'video_type' => ['nullable', 'in:youtube,vimeo,upload'],
            'video_url' => ['nullable', 'string', 'max:255'],
            'duration' => ['nullable', 'string', 'max:50'],
            'summary' => ['nullable', 'string'],
        ]);
    }
}
