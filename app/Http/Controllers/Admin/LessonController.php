<?php

namespace App\Http\Controllers\Admin;

use App\Models\Section;
use App\Models\Lesson;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

class LessonController extends BaseAdminController
{
    public function __construct(
        private readonly FileUploadService $uploads,
    ) {
    }

    public function store(Request $request, Section $section): RedirectResponse
    {
        $validated = $this->validateLesson($request);
        $validated = $this->applyUploads($request, $validated);

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
        $validated = $this->applyUploads($request, $validated, $lesson);

        return $this->tryActionBack(function () use ($lesson, $validated) {
            $lesson->update($validated);
        }, 'Lesson updated.');
    }

    /**
     * Resolves video_url and attachment against any uploaded files, mirroring
     * how CourseController handles thumbnail/preview_video uploads. Keeping
     * this separate from validateLesson() since it needs the model (for
     * replace-vs-keep-existing decisions) which validation doesn't.
     */
    private function applyUploads(Request $request, array $validated, ?Lesson $lesson = null): array
    {
        if ($validated['video_type'] === 'upload' && $request->hasFile('video_file')) {
            $oldVideo = $lesson?->video_type === 'upload' ? $lesson->video_url : null;
            $validated['video_url'] = $this->uploads->replace($oldVideo, $request->file('video_file'), 'lessons/videos');
        } elseif ($validated['video_type'] !== 'upload') {
            $validated['video_url'] = $request->input('video_url');
        } elseif ($lesson) {
            // Upload type with no new file on an edit — keep the existing stored video.
            unset($validated['video_url']);
        }

        unset($validated['video_file']);

        if ($request->hasFile('attachment_file')) {
            $validated['attachment'] = $this->uploads->replace($lesson?->attachment, $request->file('attachment_file'), 'lessons/documents');
        }

        unset($validated['attachment_file']);

        return $validated;
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
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg', 'max:51200'],
            'duration' => ['nullable', 'string', 'max:50'],
            'summary' => ['nullable', 'string'],
            'attachment_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip', 'max:20480'],
        ]);
    }
}
