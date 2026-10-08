<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Section;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class SectionController extends BaseAdminController
{
    public function store(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate(['title' => ['required', 'string', 'max:255']]);

        return $this->tryActionBack(function () use ($course, $validated) {
            $course->sections()->create([
                'title' => $validated['title'],
                'order' => $course->sections()->max('order') + 1,
            ]);
        }, 'Section added.');
    }

    public function update(Request $request, Section $section): RedirectResponse
    {
        $validated = $request->validate(['title' => ['required', 'string', 'max:255']]);

        return $this->tryActionBack(function () use ($section, $validated) {
            $section->update($validated);
        }, 'Section updated.');
    }

    public function destroy(Section $section): RedirectResponse
    {
        return $this->tryActionBack(function () use ($section) {
            $section->delete();
        }, 'Section removed.');
    }

    public function reorder(Request $request, Course $course): JsonResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);

        try {
            foreach ($validated['order'] as $index => $sectionId) {
                Section::where('id', $sectionId)
                    ->where('course_id', $course->id)
                    ->update(['order' => $index]);
            }
        } catch (Throwable $e) {
            report($e);

            return response()->json(['status' => 'error'], 500);
        }

        return response()->json(['status' => 'ok']);
    }
}
