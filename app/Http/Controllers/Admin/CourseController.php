<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\GeneratesUniqueSlugs;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
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
        return $this->renderList($request, null, 'Courses');
    }

    public function pending(Request $request): View
    {
        return $this->renderList($request, 'pending', 'Pending Courses');
    }

    private function renderList(Request $request, ?string $forcedStatus, string $title): View
    {
        $query = Course::with(['instructor', 'category'])
            ->withCount(['sections', 'lessons', 'enrollments'])
            ->latest();

        $status = $forcedStatus ?? $request->get('status');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($categoryId = $request->get('category_id')) {
            $query->where('category_id', $categoryId);
        }

        $price = $request->get('price', 'all');
        if ($price === 'free') {
            $query->where('is_free', true);
        } elseif ($price === 'paid') {
            $query->where('is_free', false);
        }

        if ($instructorId = $request->get('instructor_id')) {
            $query->where('user_id', $instructorId);
        }

        return view('admin.courses.index', [
            'courses' => $query->paginate(10)->withQueryString(),
            'categories' => Category::topLevel()->orderBy('name')->get(),
            'instructors' => User::where('is_instructor', true)->orderBy('name')->get(),
            'selectedStatus' => $status ?? 'all',
            'selectedCategory' => $request->get('category_id', 'all'),
            'selectedPrice' => $price,
            'selectedInstructor' => $request->get('instructor_id', 'all'),
            'search' => $request->get('search', ''),
            'pageTitle' => $title,
            'isPendingView' => $forcedStatus === 'pending',
            'stats' => [
                'active' => $this->trends->forModel(Course::class, fn ($q) => $q->where('status', 'active')),
                'pending' => $this->trends->forModel(Course::class, fn ($q) => $q->where('status', 'pending')),
                'free' => $this->trends->forModel(Course::class, fn ($q) => $q->where('is_free', true)),
                'paid' => $this->trends->forModel(Course::class, fn ($q) => $q->where('is_free', false)),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.courses.form', [
            'course' => new Course(),
            'categories' => Category::topLevel()->with('children')->orderBy('name')->get(),
            'instructors' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->tryAction(function () use ($request) {
            $validated = $this->validateCourse($request);
            $validated = $this->applyDerivedFields($request, $validated);
            $validated['slug'] = $this->uniqueSlug(Course::class, $validated['title']);

            $course = Course::create($validated);

            return redirect()->route('admin.courses.builder', $course);
        }, 'Course created. Now add sections and lessons.', 'admin.courses.index');
    }

    public function edit(Course $course): View
    {
        return view('admin.courses.form', [
            'course' => $course,
            'categories' => Category::topLevel()->with('children')->orderBy('name')->get(),
            'instructors' => User::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        return $this->tryAction(function () use ($request, $course) {
            $validated = $this->validateCourse($request);
            $validated = $this->applyDerivedFields($request, $validated, $course);

            if ($validated['title'] !== $course->title) {
                $validated['slug'] = $this->uniqueSlug(Course::class, $validated['title'], $course->id);
            }

            $course->update($validated);
        }, 'Course updated successfully.', 'admin.courses.index');
    }

    public function destroy(Course $course): RedirectResponse
    {
        return $this->tryAction(function () use ($course) {
            $course->delete();
        }, 'Course deleted.', 'admin.courses.index');
    }

    public function approve(Course $course): RedirectResponse
    {
        return $this->tryActionBack(function () use ($course) {
            $course->update(['status' => 'active', 'rejection_reason' => null]);
        }, "\"{$course->title}\" is now live.");
    }

    public function reject(Request $request, Course $course): RedirectResponse
    {
        $request->validate(['rejection_reason' => ['required', 'string', 'max:500']]);

        return $this->tryActionBack(function () use ($request, $course) {
            $course->update(['status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);
        }, "\"{$course->title}\" was rejected.");
    }

    public function updateStatus(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,pending,active,rejected'],
        ]);

        return $this->tryActionBack(function () use ($validated, $course) {
            $course->update([
                'status' => $validated['status'],
                'rejection_reason' => $validated['status'] === 'rejected' ? $course->rejection_reason : null,
            ]);
        }, "\"{$course->title}\" is now {$validated['status']}.");
    }

    public function builder(Course $course): View
    {
        $course->load(['sections.lessons.questions']);

        return view('admin.courses.builder', [
            'course' => $course,
        ]);
    }

    private function validateCourse(Request $request): array
    {
        return $request->validate([
            'user_id' => ['required', 'exists:users,id'],
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
            'is_top_course' => ['sometimes', 'boolean'],
            'preview_video_type' => ['required', 'in:youtube,vimeo,upload'],
            'preview_video_url' => ['nullable', 'string', 'max:255'],
            'preview_video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm,video/ogg', 'max:51200'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);
    }

    /**
     * Fields that depend on other input (toggles, uploads, free-text lists)
     * rather than simple passthrough validation.
     */
    private function applyDerivedFields(Request $request, array $validated, ?Course $course = null): array
    {
        $validated['outcomes'] = $this->linesToArray($request->input('outcomes_text'));
        $validated['requirements'] = $this->linesToArray($request->input('requirements_text'));

        // Discount price only persists when the "has discount" toggle is on.
        $validated['discount_price'] = $request->boolean('discount_flag')
            ? $validated['discount_price'] ?? null
            : null;

        $validated['is_free'] = $request->boolean('is_free');
        $validated['is_top_course'] = $request->boolean('is_top_course');

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
            // Upload type with no new file on an edit — keep the existing stored video.
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
