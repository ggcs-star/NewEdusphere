<?php

namespace App\Http\Controllers\Instructor;

use App\Concerns\GeneratesUniqueSlugs;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Category;
use App\Models\Course;
use App\Services\FileUploadService;
use App\Services\TrendStatsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends BaseAdminController
{
    use GeneratesUniqueSlugs;

    public function __construct(
        private readonly FileUploadService $uploads,
        private readonly TrendStatsService $trends,
    ) {
    }

    public function index(Request $request): View
    {
        $userId = current_admin_user()->id;

        $query = Course::where('user_id', $userId)
            ->with('category')
            ->withCount(['sections', 'lessons', 'enrollments'])
            ->latest();

        if ($status = $request->get('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($categoryId = $request->get('category_id')) {
            if ($categoryId !== 'all') {
                $query->where('category_id', $categoryId);
            }
        }

        $price = $request->get('price', 'all');
        if ($price === 'free') {
            $query->where('is_free', true);
        } elseif ($price === 'paid') {
            $query->where('is_free', false);
        }

        return view('admin.courses.index', [
            'courses' => $query->paginate(10)->withQueryString(),
            'categories' => Category::topLevel()->orderBy('name')->get(),
            'selectedCategory' => $request->get('category_id', 'all'),
            'selectedStatus' => $request->get('status', 'all'),
            'selectedPrice' => $price,
            'search' => $request->get('search', ''),
            'pageTitle' => 'My Courses',
            'isPendingView' => false,
            'isAdmin' => false,
            'routePrefix' => 'instructor',
            'stats' => [
                'active' => $this->trends->forModel(Course::class, fn ($q) => $q->where('user_id', $userId)->where('status', 'active')),
                'pending' => $this->trends->forModel(Course::class, fn ($q) => $q->where('user_id', $userId)->where('status', 'pending')),
                'free' => $this->trends->forModel(Course::class, fn ($q) => $q->where('user_id', $userId)->where('is_free', true)),
                'paid' => $this->trends->forModel(Course::class, fn ($q) => $q->where('user_id', $userId)->where('is_free', false)),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.courses.form', [
            'course' => new Course(),
            'categories' => Category::topLevel()->with('children')->orderBy('name')->get(),
            'isAdmin' => false,
            'routePrefix' => 'instructor',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->tryAction(function () use ($request) {
            $validated = $this->validateCourse($request);
            $validated = $this->applyDerivedFields($request, $validated);
            $validated['user_id'] = current_admin_user()->id;
            $validated['is_instructor'] = true;
            $validated['status'] = $request->input('action') === 'submit' ? 'pending' : 'draft';
            $validated['slug'] = $this->uniqueSlug(Course::class, $validated['title']);

            $course = Course::create($validated);

            return redirect()->route('instructor.courses.builder', $course);
        }, fn () => $request->input('action') === 'submit'
            ? 'Course submitted for review. An admin will take a look soon.'
            : 'Course saved as draft.', 'instructor.courses.index');
    }

    public function edit(Course $course): View
    {
        $this->authorizeOwnership($course);

        return view('admin.courses.form', [
            'course' => $course,
            'categories' => Category::topLevel()->with('children')->orderBy('name')->get(),
            'isAdmin' => false,
            'routePrefix' => 'instructor',
        ]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $this->authorizeOwnership($course);

        return $this->tryAction(function () use ($request, $course) {
            $validated = $this->validateCourse($request);
            $validated = $this->applyDerivedFields($request, $validated, $course);

            if ($validated['title'] !== $course->title) {
                $validated['slug'] = $this->uniqueSlug(Course::class, $validated['title'], $course->id);
            }

            // Draft courses can be kept as a draft or submitted; a resubmit
            // from rejected goes back into the review queue. Courses that
            // are already pending or live keep their current status — small
            // edits shouldn't yank an active course off the catalog.
            if (in_array($course->status, ['draft', 'rejected'])) {
                $validated['status'] = $request->input('action') === 'submit' ? 'pending' : 'draft';
                $validated['rejection_reason'] = null;
            }

            $course->update($validated);
        }, 'Course updated.', 'instructor.courses.index');
    }

    private function authorizeOwnership(Course $course): void
    {
        abort_unless($course->user_id === current_admin_user()?->id, 403);
    }

    private function validateCourse(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'language' => ['required', 'string', 'max:100'],
            'level' => ['required', 'in:beginner,intermediate,advanced,all_levels'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_flag' => ['sometimes', 'boolean'],
            'discount_price' => ['nullable', 'numeric', 'min:0', 'lt:price'],
            'is_free' => ['sometimes', 'boolean'],
            'preview_video_type' => ['required', 'in:youtube,vimeo,upload'],
            'preview_video_url' => ['nullable', 'string', 'max:255'],
            'preview_video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg', 'max:51200'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);
    }

    private function applyDerivedFields(Request $request, array $validated, ?Course $course = null): array
    {
        $validated['outcomes'] = $this->linesToArray($request->input('outcomes_text'));
        $validated['requirements'] = $this->linesToArray($request->input('requirements_text'));

        $validated['discount_price'] = $request->boolean('discount_flag')
            ? $validated['discount_price'] ?? null
            : null;

        $validated['is_free'] = $request->boolean('is_free');

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $course?->thumbnail
                ? $this->uploads->replace($course->thumbnail, $request->file('thumbnail'), 'courses')
                : $this->uploads->store($request->file('thumbnail'), 'courses');
        }

        if ($validated['preview_video_type'] === 'upload' && $request->hasFile('preview_video_file')) {
            $oldVideo = $course?->preview_video_type === 'upload' ? $course->preview_video_url : null;
            $validated['preview_video_url'] = $this->uploads->replace($oldVideo, $request->file('preview_video_file'), 'courses/videos');
        } elseif ($validated['preview_video_type'] !== 'upload') {
            $validated['preview_video_url'] = $request->input('preview_video_url');
        } elseif ($course) {
            unset($validated['preview_video_url']);
        }

        unset($validated['preview_video_file']);

        return $validated;
    }

    private function linesToArray(?string $text): array
    {
        if (blank($text)) {
            return [];
        }

        return collect(explode("\n", $text))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}
