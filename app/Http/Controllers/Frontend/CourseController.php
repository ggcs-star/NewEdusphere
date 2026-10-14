<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::with(['instructor', 'category'])
            ->withCount(['sections', 'lessons', 'enrollments'])
            ->where('status', 'active');

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%");
            });
        }
        if ($category = $request->integer('category')) {
            $query->where(function ($builder) use ($category) {
                $builder->where('category_id', $category)->orWhere('sub_category_id', $category);
            });
        }
        if ($request->query('price') === 'free') $query->where('is_free', true);
        if ($request->query('price') === 'paid') $query->where('is_free', false);

        return view('frontend.courses.index', [
            'courses' => $query->latest()->paginate(12)->withQueryString(),
            'categories' => Category::where('status', true)->orderBy('name')->get(),
        ]);
    }

    public function show(string $slug, int $id): View
    {
        $course = Course::with([
            'instructor', 'category', 'subCategory',
            'sections.lessons',
        ])->where('status', 'active')->where('id', $id)->firstOrFail();

        // Slugs are descriptive URLs; ID is authoritative for compatibility with existing links.
        abort_if($slug !== $course->slug, 404);

        $relatedCourses = Course::with(['instructor', 'category'])
            ->where('status', 'active')->where('category_id', $course->category_id)
            ->whereKeyNot($course->id)->latest()->take(4)->get();

        return view('frontend.courses.show', compact('course', 'relatedCourses'));
    }

    public function categories(): View
    {
        $categories = Category::where('status', true)->withCount('children')
            ->orderBy('name')->paginate(18);

        return view('frontend.categories.index', compact('categories'));
    }
}
