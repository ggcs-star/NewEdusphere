<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    /**
     * @param  bool  $isCourseUnlocked  Whether every lesson is accessible (free or purchased course).
     * @param  array<int>  $previewLessonIds  Lesson ids that are free previews regardless of purchase.
     */
    public function __construct(
        $resource,
        private readonly bool $isCourseUnlocked,
        private readonly array $previewLessonIds
    ) {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'order' => (int) $this->order,
            'lessons' => $this->lessons
                ->sortBy(fn ($lesson) => (int) $lesson->order)
                ->values()
                ->map(fn ($lesson) => (new LessonResource(
                    $lesson,
                    $this->isCourseUnlocked || in_array((int) $lesson->id, $this->previewLessonIds, true)
                ))->resolve())
                ->all(),
        ];
    }
}
