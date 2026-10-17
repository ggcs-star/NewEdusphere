<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CertificateIssuanceService
{
    public function issueIfEligible(int $userId, int $courseId): ?Certificate
    {
        return DB::transaction(function () use ($userId, $courseId) {

            // Return an existing certificate if already issued.
            $existing = Certificate::query()
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->first();

            if ($existing) {
                return $existing;
            }

            // Student must be enrolled.
            $enrolled = DB::table('enrollments')
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->exists();

            if (! $enrolled) {
                return null;
            }

            // Load course and lessons.
            $course = Course::with('sections.lessons')
                ->find($courseId);

            if (! $course) {
                return null;
            }

            // Course must have an assigned active template.
            $templateId = $course->certificate_template_id;

            if (! $templateId) {
                return null;
            }

            $template = CertificateTemplate::query()
                ->whereKey($templateId)
                ->where('is_active', true)
                ->first();

            if (! $template) {
                return null;
            }

            $lessons = $course->sections
                ->flatMap(fn ($section) => $section->lessons);

            if ($lessons->isEmpty()) {
                return null;
            }

            // Every lesson and quiz must be completed.
            foreach ($lessons as $lesson) {

                if ($lesson->lesson_type === 'quiz') {
                    $quizCompleted = QuizAttempt::query()
                        ->where('user_id', $userId)
                        ->where('lesson_id', $lesson->id)
                        ->exists();

                    if (! $quizCompleted) {
                        return null;
                    }

                    continue;
                }

                $lessonCompleted = LessonProgress::query()
                    ->where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->where('lesson_id', $lesson->id)
                    ->where('is_completed', true)
                    ->exists();

                if (! $lessonCompleted) {
                    return null;
                }
            }

            // Generate a unique certificate number.
            $certificateNumber = 'EDU-' . now()->format('Ymd')
                . '-' . strtoupper(Str::random(8));

            return Certificate::create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'certificate_template_id' => $template->id,
                'certificate_number' => $certificateNumber,
                'data' => [
                    'student_name' => DB::table('users')
                        ->where('id', $userId)
                        ->value('name') ?? 'Student',
                    'course_name' => $course->title,
                    'completion_date' => now()->toDateString(),
                    'certificate_number' => $certificateNumber,
                ],
                'issued_at' => now(),
            ]);
        });
    }
}
