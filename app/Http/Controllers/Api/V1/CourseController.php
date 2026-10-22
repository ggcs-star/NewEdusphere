<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CourseDetailResource;
use App\Http\Resources\CourseResource;
use App\Models\User;
use App\Services\CourseCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mirrors App\Http\Controllers\Frontend\CourseController — both share
 * CourseCatalogService so the catalog/access rules can't drift between
 * the Blade site and this JSON API.
 */
class CourseController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly CourseCatalogService $catalog)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $courses = $this->catalog->listCourses($request->only('q', 'category', 'price', 'per_page'));

        return $this->ok([
            'courses' => CourseResource::collection($courses),
            'categories' => CategoryResource::collection($this->catalog->activeCategories()),
            'pagination' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'total' => $courses->total(),
            ],
        ]);
    }

    public function show(Request $request, string $slug, int $id): JsonResponse
    {
        $detail = $this->catalog->courseDetail($slug, $id, $this->optionalUser($request));

        return $this->ok(new CourseDetailResource($detail));
    }

    /**
     * Resolve the Passport-authenticated user if a valid Bearer token was
     * sent, without forcing authentication — the catalog is browsable by
     * guests, same as the web routes.
     */
    private function optionalUser(Request $request): ?User
    {
        try {
            return $request->user('api');
        } catch (\Throwable) {
            return null;
        }
    }
}
