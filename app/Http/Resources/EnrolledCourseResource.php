<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * A CourseResource plus the viewer's certificate_id for that course
 * (set by MyCoursesService::enrolledCourses()) — used on the "My
 * Courses" list, where every course is already known to be enrolled.
 */
class EnrolledCourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return array_merge(
            (new CourseResource($this->resource))->resolve($request),
            [
                'certificate_id' => $this->certificate_id ?? null,
            ]
        );
    }
}
