<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Full single-course payload, built from the array CourseCatalogService::
 * courseDetail() returns. Extends the catalog fields from CourseResource
 * with the curriculum (sections/lessons) and viewer-specific state.
 */
class CourseDetailResource extends JsonResource
{
    public function __construct(private readonly array $detail)
    {
        parent::__construct($detail['course']);
    }

    public function toArray(Request $request): array
    {
        $course = $this->detail['course'];

        return array_merge(
            (new CourseResource($course))->resolve($request),
            [
                'description' => $course->description,
                'outcomes' => $course->outcomes,
                'requirements' => $course->requirements,
                'preview_video_url' => $course->preview_video_url,
                'sub_category' => new CategoryResource($this->whenLoaded('subCategory')),
                'sections' => $this->detail['sections']
                    ->map(fn ($section) => (new SectionResource(
                        $section,
                        $this->detail['isCourseUnlocked'],
                        $this->detail['previewLessonIds']
                    ))->resolve($request))
                    ->values()
                    ->all(),
                'related_courses' => CourseResource::collection($this->detail['relatedCourses']),
                'is_enrolled' => $this->detail['isEnrolled'],
                'is_unlocked' => $this->detail['isCourseUnlocked'],
                'preview_lesson_ids' => $this->detail['previewLessonIds'],
                'quiz_score_summary' => $this->detail['quizScoreSummary'],
            ]
        );
    }
}
