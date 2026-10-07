<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\GeneratesUniqueSlugs;
use App\Models\Category;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends BaseAdminController
{
    use GeneratesUniqueSlugs;

    public function __construct(private readonly FileUploadService $uploads)
    {
    }

    public function index(Request $request): View
    {
        $query = Category::topLevel()->with('children')->latest();

        if ($search = $request->get('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $status = $request->get('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status === 'active');
        }

        return view('admin.categories.index', [
            'categories' => $query->paginate(9)->withQueryString(),
            'allCategories' => Category::topLevel()->orderBy('name')->get(),
            'search' => $search ?? '',
            'selectedStatus' => $status,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->tryAction(function () use ($request) {
            $this->normalizeParentId($request);
            $validated = $this->validateCategory($request);

            $category = new Category();
            $category->name = $validated['name'];
            $category->slug = $this->uniqueSlug(Category::class, $validated['name']);
            $category->code = Str::upper(Str::random(8));
            $category->parent_id = $validated['parent_id'] ?? null;

            if (blank($category->parent_id)) {
                $category->icon = ($validated['icon'] ?? null) ?: 'fa-solid fa-shapes';

                if ($request->hasFile('thumbnail')) {
                    $category->thumbnail = $this->uploads->store($request->file('thumbnail'), 'categories');
                }
            }

            $category->save();
        }, 'Category has been added successfully.', 'admin.categories.index');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->normalizeParentId($request);

        if ((int) $request->input('parent_id') === $category->id) {
            return back()->with('flash_error', 'A category cannot be its own parent.');
        }

        return $this->tryAction(function () use ($request, $category) {
            $validated = $this->validateCategory($request);

            $category->name = $validated['name'];
            $category->slug = $this->uniqueSlug(Category::class, $validated['name'], $category->id);
            $category->parent_id = $validated['parent_id'] ?? null;

            if (blank($category->parent_id)) {
                $category->icon = ($validated['icon'] ?? null) ?: $category->icon;

                if ($request->hasFile('thumbnail')) {
                    $category->thumbnail = $this->uploads->replace($category->thumbnail, $request->file('thumbnail'), 'categories');
                }
            }

            $category->save();
        }, 'Category has been updated successfully.', 'admin.categories.index');
    }

    public function destroy(Category $category): RedirectResponse
    {
        return $this->tryAction(function () use ($category) {
            $category->children()->delete();
            $category->delete();
        }, 'Category has been deleted.', 'admin.categories.index');
    }

    private function validateCategory(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'icon' => ['nullable', 'string', 'max:100'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);
    }

    private function normalizeParentId(Request $request): void
    {
        if ((int) $request->input('parent_id') === 0) {
            $request->merge(['parent_id' => null]);
        }
    }
}
