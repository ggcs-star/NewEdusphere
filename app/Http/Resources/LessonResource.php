<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A lesson's real content (video, attachment, questions) is only
 * exposed when $accessible is true — i.e. the viewer has course
 * access, or this lesson is one of the free previews. Locked lessons
 * still list their title/type/order so the curriculum outline renders,
 * just without anything that would let a client bypass payment.
 */
class LessonResource extends JsonResource
{
    public function __construct($resource, private readonly bool $accessible)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'lesson_type' => $this->lesson_type,
            'order' => (int) $this->order,
            'duration' => $this->duration,
            'total_score' => (int) $this->total_score,
            'is_locked' => ! $this->accessible,
            'video_type' => $this->when($this->accessible, $this->video_type),
            'video_url' => $this->when($this->accessible, fn () => $this->embedUrl()),
            'attachment_url' => $this->when(
                $this->accessible && $this->attachment,
                fn () => \Illuminate\Support\Facades\Storage::url($this->attachment)
            ),
            'summary' => $this->when($this->accessible, $this->summary),
            'questions_count' => $this->lesson_type === 'quiz' ? $this->questions->count() : null,
        ];
    }
}
