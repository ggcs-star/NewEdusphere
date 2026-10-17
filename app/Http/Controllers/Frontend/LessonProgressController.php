<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\LessonProgress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LessonProgressController extends Controller
{
    private function viewer()
    {
        return function_exists('current_admin_user')
            ? current_admin_user()
            : null;
    }

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

    public function show(int $courseId, int $lessonId): JsonResponse
    {
        $viewer = $this->viewer();

        abort_unless($viewer && $viewer->id, 401);

        $course = Course::with('sections.lessons')
            ->whereKey($courseId)
            ->firstOrFail();

        abort_unless($this->canAccess($course, $viewer), 403);

        $lesson = $course->sections
            ->flatMap(fn ($section) => $section->lessons)
            ->firstWhere('id', $lessonId);

        abort_unless($lesson, 404);

        $progress = LessonProgress::where('user_id', $viewer->id)
            ->where('course_id', $course->id)
            ->where('lesson_id', $lessonId)
            ->first();

        return response()->json([
            'position' => $progress?->last_position_seconds ?? 0,
            'duration' => $progress?->duration_seconds ?? 0,
            'completed' => $progress?->is_completed ?? false,
        ]);
    }

    public function save(Request $request, int $courseId, int $lessonId): JsonResponse
    {
        $data = $request->validate([
            'position' => ['required', 'integer', 'min:0'],
            'duration' => ['required', 'integer', 'min:0'],
            'completed' => ['sometimes', 'boolean'],
        ]);

        $viewer = $this->viewer();

        abort_unless($viewer && $viewer->id, 401);

        $course = Course::with('sections.lessons')
            ->whereKey($courseId)
            ->firstOrFail();

        abort_unless($this->canAccess($course, $viewer), 403);

        $lesson = $course->sections
            ->flatMap(fn ($section) => $section->lessons)
            ->firstWhere('id', $lessonId);

        abort_unless($lesson, 404);

        $duration = (int) $data['duration'];

        // Unknown duration (player not ready yet): never overwrite the stored
        // position, because min(position, 0) would reset it to 0.
        if ($duration <= 0) {
            return response()->json(['success' => false, 'ignored' => true]);
        }

        $position = min((int) $data['position'], $duration);

        $completed = (bool) ($data['completed'] ?? false);

        // Mark complete only when playback reaches near the end.
        if ($duration > 0 && $position >= ($duration - 5)) {
            $completed = true;
        }

        $progress = LessonProgress::firstOrNew([
            'user_id' => $viewer->id,
            'lesson_id' => $lessonId,
        ]);

        $progress->course_id = $course->id;
        $progress->last_position_seconds = $position;
        $progress->duration_seconds = $duration;
        $progress->is_completed = $progress->is_completed || $completed;
        $progress->last_watched_at = now();
        $progress->save();

        return response()->json([
            'success' => true,
            'position' => $progress->last_position_seconds,
            'completed' => $progress->is_completed,
        ]);
    }
}