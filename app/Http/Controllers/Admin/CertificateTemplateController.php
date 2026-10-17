<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use App\Models\Course;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CertificateTemplateController extends Controller
{
    public function __construct(
        private readonly FileUploadService $uploads
    ) {
    }

    /**
     * Display certificate templates.
     */
    public function index(Request $request): View
    {
        $templates = CertificateTemplate::query()
            ->withCount('courses')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where('title', 'like', "%{$search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where(
                    'is_active',
                    $request->input('status') === 'active'
                );
            })
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.certificates.index', compact('templates'));
    }

    /**
     * Show certificate creation form.
     */
    public function create(): View
    {
        return view('admin.certificates.form', [
            'template' => new CertificateTemplate(),
            'courses' => $this->availableCourses(),
            'assignedCourseIds' => [],
        ]);
    }

    /**
     * Store a new certificate template.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateTemplate($request, true);

        $imagePath = $this->uploads->store(
            $request->file('background_image'),
            'certificates'
        );

        try {
            DB::transaction(function () use ($data, $imagePath) {
                // The CertificateTemplate model's array cast
                // automatically converts this array to JSON.
                $template = CertificateTemplate::create([
                    'title' => $data['title'],
                    'background_image' => $imagePath,
                    'fields' => $data['fields'],
                    'is_active' => $data['is_active'],
                    'sort_order' => $data['sort_order'],
                ]);

                $this->assignCourses(
                    $template,
                    $data['course_ids']
                );
            });
        } catch (\Throwable $exception) {
            $this->uploads->delete($imagePath);

            throw $exception;
        }

        return redirect()
            ->route('admin.certificates.index')
            ->with('status', 'Certificate template created successfully.');
    }

    /**
     * Show certificate editing form.
     */
    public function edit(CertificateTemplate $certificateTemplate): View
    {
        return view('admin.certificates.form', [
            'template' => $certificateTemplate,
            'courses' => $this->availableCourses(),
            'assignedCourseIds' => $certificateTemplate->courses()
                ->pluck('courses.id')
                ->map(fn ($id) => (int) $id)
                ->all(),
        ]);
    }

    /**
     * Display a single certificate template.
     */
    public function show(CertificateTemplate $certificateTemplate): View
    {
        $courses = Course::query()
            ->where('certificate_template_id', $certificateTemplate->id)
            ->orderBy('title')
            ->get(['id', 'title']);

        return view('admin.certificates.show', [
            'template' => $certificateTemplate,
            'courses' => $courses,
        ]);
    }

    /**
     * Update an existing certificate template.
     */
    public function update(
        Request $request,
        CertificateTemplate $certificateTemplate
    ): RedirectResponse {
        $data = $this->validateTemplate($request, false);

        $oldImage = $certificateTemplate->background_image;
        $newImage = null;

        if ($request->hasFile('background_image')) {
            $newImage = $this->uploads->store(
                $request->file('background_image'),
                'certificates'
            );
        }

        try {
            DB::transaction(function () use (
                $certificateTemplate,
                $data,
                $newImage
            ) {
                $updateData = [
                    'title' => $data['title'],
                    // Keep this as an array; the model cast handles JSON.
                    'fields' => $data['fields'],
                    'is_active' => $data['is_active'],
                    'sort_order' => $data['sort_order'],
                ];

                if ($newImage !== null) {
                    $updateData['background_image'] = $newImage;
                }

                $certificateTemplate->update($updateData);

                $this->assignCourses(
                    $certificateTemplate,
                    $data['course_ids']
                );
            });
        } catch (\Throwable $exception) {
            if ($newImage !== null) {
                $this->uploads->delete($newImage);
            }

            throw $exception;
        }

        if ($newImage !== null && $oldImage) {
            $this->uploads->delete($oldImage);
        }

        return redirect()
            ->route('admin.certificates.index')
            ->with('status', 'Certificate template updated successfully.');
    }

    /**
     * Delete a certificate template.
     */
    public function destroy(
        CertificateTemplate $certificateTemplate
    ): RedirectResponse {
        if ($certificateTemplate->courses()->exists()) {
            return back()->with(
                'error',
                'This template is assigned to courses. Remove its course assignments before deleting it.'
            );
        }

        $image = $certificateTemplate->background_image;

        DB::transaction(function () use ($certificateTemplate) {
            $certificateTemplate->delete();
        });

        if ($image) {
            $this->uploads->delete($image);
        }

        return redirect()
            ->route('admin.certificates.index')
            ->with('status', 'Certificate template deleted successfully.');
    }

    /**
     * Validate certificate template form data.
     */
    private function validateTemplate(
        Request $request,
        bool $creating
    ): array {
        $data = $request->validate([
            'title' => [
                'required',
                'string',
                'max:191',
            ],

            'background_image' => [
                $creating ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            'fields' => [
                'nullable',
                'array',
            ],

            'fields.*' => [
                'string',
                Rule::in([
                    'company_name',
                    'student_name',
                    'course_name',
                    'completion_date',
                    'certificate_number',
                    'signer_name',
                    'signer_designation',
                ]),
            ],

            'course_ids' => [
                'nullable',
                'array',
            ],

            'course_ids.*' => [
                'integer',
                'distinct',
                'exists:courses,id',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $data['fields'] = array_values(
            array_unique($data['fields'] ?? [])
        );

        $data['course_ids'] = array_values(
            array_unique(
                array_map('intval', $data['course_ids'] ?? [])
            )
        );

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    /**
     * Get available courses.
     */
    private function availableCourses()
    {
        return Course::query()
            ->orderBy('title')
            ->get([
                'id',
                'title',
                'certificate_template_id',
            ]);
    }

    /**
     * Assign multiple courses to a template.
     */
    private function assignCourses(
        CertificateTemplate $template,
        array $courseIds
    ): void {
        $currentAssignments = Course::query()
            ->where('certificate_template_id', $template->id);

        if (!empty($courseIds)) {
            $currentAssignments->whereNotIn('id', $courseIds);
        }

        $currentAssignments->update([
            'certificate_template_id' => null,
        ]);

        if (!empty($courseIds)) {
            Course::query()
                ->whereIn('id', $courseIds)
                ->update([
                    'certificate_template_id' => $template->id,
                ]);
        }
    }
}
