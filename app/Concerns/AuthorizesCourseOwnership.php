<?php

namespace App\Concerns;

use App\Models\Course;

/**
 * Shared by the course-content controllers (Section/Lesson/Question) and
 * CourseController so the same methods can be mounted under both the
 * admin route group (any course) and the instructor route group
 * (own courses only) without duplicating the CRUD logic.
 */
trait AuthorizesCourseOwnership
{
    protected function authorizeCourseAccess(Course $course): void
    {
        $user = auth()->user();

        abort_unless($user && ($user->isAdmin() || $course->user_id === $user->id), 403);
    }
}
