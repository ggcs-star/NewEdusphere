<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryDetailResource;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mirrors the web "/categories" page and Frontend\CourseController::
 * categories() — all three share CategoryCatalogService so the active/
 * status filtering rule can't drift between surfaces.
 */
class CategoryController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly CategoryCatalogService $categories)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->filled('per_page') ? $request->integer('per_page') : null;
        $categories = $this->categories->listCategories($perPage);

        return $this->ok([
            'categories' => CategoryResource::collection($categories),
        ]);
    }

    public function show(Request $request, string $slug): JsonResponse
    {
        $detail = $this->categories->categoryDetail($slug, $request->only('q', 'price', 'per_page'));

        return $this->ok(new CategoryDetailResource($detail));
    }
}
