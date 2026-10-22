<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Resources\EnrolledCourseResource;
use App\Http\Resources\PaymentResource;
use App\Services\MyCoursesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mirrors Frontend\MyCoursesController and the web "/dashboard" and
 * "/purchase-history" routes — all four share MyCoursesService.
 */
class MyCoursesController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly MyCoursesService $myCourses)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user('api');

        $courses = $this->myCourses->enrolledCourses($user, (int) $request->integer('per_page', 9) ?: 9);
        $summary = $this->myCourses->summary($user);

        return $this->ok([
            'courses' => EnrolledCourseResource::collection($courses),
            'pagination' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ],
            'summary' => [
                'total_enrollments' => $summary['total_enrollments'],
                'total_spent' => $summary['total_spent'],
                'successful_payments' => $summary['successful_payments'],
            ],
        ]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user('api');

        $summary = $this->myCourses->dashboardSummary($user);

        return $this->ok([
            'recent_courses' => CourseResource::collection($summary['courses']),
            'enrolled_count' => $summary['enrolled_count'],
        ]);
    }

    public function purchaseHistory(Request $request): JsonResponse
    {
        $user = $request->user('api');

        return $this->ok(
            PaymentResource::collection($this->myCourses->purchaseHistory($user))
        );
    }
}
