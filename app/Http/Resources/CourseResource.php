<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Summary shape used for course listings (catalog, related courses,
 * my-courses, cart). CourseDetailResource extends this for the full
 * single-course payload instead of duplicating these fields.
 */
class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'short_description' => $this->short_description,
            'thumbnail_url' => $this->thumbnailUrl(),
            'level' => $this->level,
            'language' => $this->language,
            'is_free' => (bool) $this->is_free,
            'price' => (float) $this->price,
            'discount_price' => $this->discount_price !== null ? (float) $this->discount_price : null,
            'effective_price' => $this->effectivePrice(),
            'has_discount' => $this->hasDiscount(),
            'discount_percent' => $this->discountPercent(),
            'instructor' => new InstructorResource($this->whenLoaded('instructor')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'sections_count' => $this->whenCounted('sections'),
            'lessons_count' => $this->whenCounted('lessons'),
            'enrollments_count' => $this->whenCounted('enrollments'),
        ];
    }
}
