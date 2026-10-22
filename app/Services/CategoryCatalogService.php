<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Category listing/detail logic shared by the web (Blade) category pages
 * and the Api\V1 category controller — mirrors CourseCatalogService so
 * both catalog surfaces follow the same pattern.
 */
class CategoryCatalogService
{
    public function __construct(private readonly CourseCatalogService $courses)
    {
    }

    /**
     * Active top-level-and-nested categories, each with its children count.
     * Pass $perPage to paginate (web "/categories" page), or omit it to
     * get the full collection (category dropdowns/filters).
     */
    public function listCategories(?int $perPage = null): Collection|LengthAwarePaginator
    {
        $query = Category::where('status', true)
            ->withCount('children')
            ->orderBy('name');

        return $perPage
            ? $query->paginate($perPage)->withQueryString()
            : $query->get();
    }

    /**
     * A single active category plus the active courses filed under it
     * (matching courses where category_id or sub_category_id = this
     * category — the same rule CourseCatalogService::listCourses()
     * applies for the "category" filter).
     */
    public function categoryDetail(string $slug, array $filters = []): array
    {
        $category = Category::where('status', true)
            ->withCount('children')
            ->where('slug', $slug)
            ->firstOrFail();

        $courses = $this->courses->listCourses(array_merge($filters, [
            'category' => $category->id,
        ]));

        return [
            'category' => $category,
            'courses' => $courses,
        ];
    }
}
