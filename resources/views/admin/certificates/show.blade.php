<x-layouts.admin :title="$template->exists ? 'Edit Certificate Template' : 'Create Certificate Template'">

    @php
$availableFields = [
    'company_name' => 'Company / Institute Name',
    'student_name' => 'Student Name',
    'course_name' => 'Course Name',
    'completion_date' => 'Completion Date',
    'certificate_number' => 'Certificate Number',
    'signer_name' => 'Manager / Signatory Name',
    'signer_designation' => 'Signatory Designation',
];

        $savedFields = $template->fields ?? [];

        if (is_string($savedFields)) {
            $savedFields = json_decode($savedFields, true) ?? [];
        }

        if (is_string($savedFields)) {
            $savedFields = json_decode($savedFields, true) ?? [];
        }

        if (!is_array($savedFields)) {
            $savedFields = [];
        }

        $selectedFields = old('fields', $savedFields ?: array_keys($availableFields));

        if (!is_array($selectedFields)) {
            $selectedFields = [];
        }

        $selectedFields = array_values(array_filter(
            $selectedFields,
            fn ($field) => is_string($field) && array_key_exists($field, $availableFields)
        ));

        $selectedCourses = collect(old('course_ids', $assignedCourseIds ?? []))
            ->map(fn ($id) => (int) $id)
            ->all();

        $isEditing = $template->exists;

        $backgroundUrl = $template->background_image
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($template->background_image)
            : null;
    @endphp

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <a href="{{ route('admin.certificates.index') }}"
                   class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:text-brand-700">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>

                <div>
                    <h2 class="text-2xl font-bold text-brand-950">
                        {{ $isEditing ? 'Edit Certificate Template' : 'Create Certificate Template' }}
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Configure the certificate design, fields and course assignments.
                    </p>
                </div>
            </div>

            @if($isEditing)
                <span class="inline-flex w-fit items-center gap-2 rounded-full bg-brand-50 px-3 py-2 text-xs font-semibold text-brand-700">
                    <i class="fa-solid fa-award"></i>
                    Template #{{ $template->id }}
                </span>
            @endif
        </div>

        {{-- Flash messages --}}
        @if(session('status'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">
                <p class="text-sm font-semibold text-rose-700">
                    Please fix the following errors.
                </p>
                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-rose-600">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $isEditing
                    ? route('admin.certificates.update', $template)
                    : route('admin.certificates.store') }}"
              enctype="multipart/form-data">

            @csrf

            @if($isEditing)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-12">

                {{-- Main column --}}
                <div class="space-y-6 xl:col-span-8">

                    {{-- Template details --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                    <i class="fa-solid fa-file-signature"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-brand-950">Template Details</h3>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Set the template title and background image.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-5 p-5 sm:p-6">

                            {{-- Title --}}
                            <div>
                                <label for="title" class="mb-2 block text-sm font-semibold text-slate-700">
                                    Template Name <span class="text-rose-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    maxlength="191"
                                    required
                                    value="{{ old('title', $template->title ?? '') }}"
                                    placeholder="e.g. Professional Course Certificate"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                                >

                                @error('title')
                                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Background upload --}}
                            <div>
                                <label for="background_image" class="mb-2 block text-sm font-semibold text-slate-700">
                                    Certificate Background
                                    @unless($isEditing)
                                        <span class="text-rose-500">*</span>
                                    @endunless
                                </label>

                                <label for="background_image"
                                       class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 px-5 py-8 text-center transition hover:border-brand-300 hover:bg-brand-50/30">

                                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-brand-600 shadow-sm">
                                        <i class="fa-solid fa-cloud-arrow-up text-xl"></i>
                                    </span>

                                    <span class="mt-3 text-sm font-semibold text-slate-700">
                                        Choose certificate background
                                    </span>

                                    <span class="mt-1 text-xs text-slate-500">
                                        JPG, JPEG, PNG or WebP · Maximum 5 MB
                                    </span>

                                    <input
                                        type="file"
                                        id="background_image"
                                        name="background_image"
                                        accept=".jpg,.jpeg,.png,.webp"
                                        class="sr-only"
                                        {{ $isEditing ? '' : 'required' }}
                                    >
                                </label>

                                <p id="selectedFileName" class="mt-2 text-xs text-slate-500"></p>

                                @error('background_image')
                                    <p class="mt-1.5 text-xs text-rose-600">{{ $message }}</p>
                                @enderror

                                @if($backgroundUrl)
                                    <div class="mt-4">
                                        <p class="mb-2 text-sm font-semibold text-slate-700">
                                            Current Background
                                        </p>
                                        <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50 p-2">
                                            <img
                                                src="{{ $backgroundUrl }}"
                                                alt="Current certificate background"
                                                class="max-h-80 w-full rounded-lg object-contain"
                                            >
                                        </div>
                                    </div>
                                @endif

                                <div id="newImagePreview" class="mt-4 hidden">
                                    <p class="mb-2 text-sm font-semibold text-slate-700">
                                        New Background Preview
                                    </p>
                                    <img
                                        id="newImagePreviewImage"
                                        alt="New certificate background preview"
                                        class="max-h-80 w-full rounded-xl border border-slate-200 object-contain"
                                    >
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Dynamic fields --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                    <i class="fa-solid fa-list-check"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-brand-950">Dynamic Certificate Fields</h3>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Select the data that should appear on the certificate.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                @foreach($availableFields as $key => $label)
                                    <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-4 transition hover:border-brand-200 hover:bg-brand-50/30">
                                        <input
                                            type="checkbox"
                                            name="fields[]"
                                            value="{{ $key }}"
                                            class="certificate-field h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                            @checked(in_array($key, $selectedFields, true))
                                        >

                                        <span class="flex-1">
                                            <span class="block text-sm font-semibold text-slate-700">
                                                {{ $label }}
                                            </span>
                                            <span class="mt-0.5 block text-xs text-slate-400">
                                                {{ $key }}
                                            </span>
                                        </span>

                                        <i class="fa-solid fa-check text-xs text-brand-600"></i>
                                    </label>
                                @endforeach
                            </div>

                            @error('fields')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror

                            @error('fields.*')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </section>

                    {{-- Live certificate preview --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                    <i class="fa-solid fa-eye"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-brand-950">Live Certificate Preview</h3>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Sample values shown over your selected background.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 sm:p-6">
                            <div id="certificatePreview"
                                 class="relative mx-auto w-full overflow-hidden bg-slate-50"
                                 style="aspect-ratio: 1.414 / 1;">

                                @if($backgroundUrl)
                                    <img
                                        id="certificatePreviewBackground"
                                        src="{{ $backgroundUrl }}"
                                        alt="Certificate preview background"
                                        class="absolute inset-0 h-full w-full object-fill"
                                    >
                                @else
                                    <img
                                        id="certificatePreviewBackground"
                                        alt="Certificate preview background"
                                        class="absolute inset-0 hidden h-full w-full object-fill"
                                    >
                                    <div id="previewPlaceholder"
                                         class="absolute inset-0 flex flex-col items-center justify-center border-2 border-dashed border-slate-300 p-4 text-center">
                                        <i class="fa-solid fa-image text-3xl text-slate-300"></i>
                                        <p class="mt-3 text-sm font-semibold text-slate-500">
                                            Upload a background to preview your certificate
                                        </p>
                                    </div>
                                @endif

                            <div id="certificatePreviewContent"
     class="absolute inset-0 flex flex-col items-center justify-center px-[12%] text-center"
     style="gap: 2%;">

    <div data-field="company_name"
         class="preview-field text-[clamp(9px,1.5vw,17px)] font-bold uppercase tracking-widest text-amber-800">
        EduSphere
    </div>

    <div class="text-[clamp(17px,3.2vw,38px)] font-extrabold uppercase tracking-wider text-slate-800">
        Certificate
    </div>

    <div class="text-[clamp(9px,1.5vw,17px)] font-semibold uppercase tracking-[0.2em] text-amber-700">
        Of Completion
    </div>

    <div class="text-[clamp(7px,1vw,12px)] text-slate-600">
        This certificate is proudly presented to
    </div>

    <div id="previewStudentName"
         data-field="student_name"
         class="preview-field text-[clamp(17px,2.8vw,32px)] font-bold text-slate-900">
        Student Name
    </div>

    <div class="h-px w-2/3 bg-amber-500"></div>

    <div class="text-[clamp(7px,1vw,12px)] text-slate-600">
        For successfully completing
    </div>

    <div id="previewCourseName"
         data-field="course_name"
         class="preview-field text-[clamp(11px,1.8vw,21px)] font-semibold text-slate-800">
        Course Name
    </div>

    <div id="previewCompletionDate"
         data-field="completion_date"
         class="preview-field text-[clamp(7px,1vw,12px)] text-slate-600">
        Date of Completion: {{ now()->format('d M Y') }}
    </div>

    <div class="mt-2 flex w-full items-end justify-between gap-4">
        <div class="preview-field flex-1" data-field="signer_name">
            <div class="mb-1 text-[clamp(9px,1.3vw,15px)] font-semibold text-slate-800">
                Manager Name
            </div>
            <div class="mx-auto w-4/5 border-t border-slate-500"></div>
            <div data-field="signer_designation"
                 class="preview-field mt-1 text-[clamp(6px,0.9vw,11px)] text-slate-600">
                Authorized Signatory
            </div>
        </div>

        <div class="preview-field flex-1" data-field="certificate_number">
            <div class="text-[clamp(6px,0.9vw,11px)] text-slate-500">
                Certificate ID
            </div>
            <div class="mt-1 text-[clamp(7px,1vw,12px)] font-semibold text-slate-800">
                CERT-0001
            </div>
        </div>
    </div>
</div>
                            </div>

                            <p class="mt-3 text-xs leading-5 text-slate-400">
                                This is a sample layout preview, not an issued certificate. Actual student details and certificate number must be supplied by the certificate issuance flow.
                            </p>
                        </div>
                    </section>

                    {{-- Course assignments --}}
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                                    <i class="fa-solid fa-book-open"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-brand-950">Assign Courses</h3>
                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Select multiple courses for this template.
                                    </p>
                                </div>
                            </div>

                            <span id="selectedCourseCount"
                                  class="inline-flex w-fit items-center rounded-full bg-brand-50 px-3 py-1.5 text-xs font-semibold text-brand-700">
                                0 selected
                            </span>
                        </div>

                        <div class="p-5 sm:p-6">
                            <div class="relative mb-4">
                                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>
                                <input
                                    type="search"
                                    id="courseSearch"
                                    placeholder="Search courses by title..."
                                    class="w-full rounded-xl border border-slate-200 py-3 pl-10 pr-4 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                                >
                            </div>

                            <div class="overflow-hidden rounded-xl border border-slate-200">
                                <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-4 py-3">
                                    <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                        Available Courses
                                    </span>

                                    <button
                                        type="button"
                                        id="toggleVisibleCourses"
                                        class="text-xs font-semibold text-brand-600 hover:text-brand-700"
                                    >
                                        Select visible
                                    </button>
                                </div>

                                <div id="courseList" class="max-h-[420px] overflow-y-auto divide-y divide-slate-100">
                                    @forelse($courses as $course)
                                        <label
                                            class="course-option flex cursor-pointer items-start gap-3 px-4 py-4 transition hover:bg-slate-50"
                                            data-course-title="{{ strtolower($course->title) }}"
                                        >
                                            <input
                                                type="checkbox"
                                                name="course_ids[]"
                                                value="{{ $course->id }}"
                                                class="course-checkbox mt-1 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                                @checked(in_array((int) $course->id, $selectedCourses, true))
                                            >

                                            <span class="min-w-0 flex-1">
                                                <span class="block text-sm font-semibold text-slate-700">
                                                    {{ $course->title }}
                                                </span>
                                                <span class="mt-1 block text-xs text-slate-400">
                                                    Course ID: {{ $course->id }}
                                                </span>

                                                @if(
                                                    $course->certificate_template_id &&
                                                    (int) $course->certificate_template_id !== (int) $template->id
                                                )
                                                    <span class="mt-2 inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-1 text-[11px] font-medium text-amber-700">
                                                        <i class="fa-solid fa-circle-info"></i>
                                                        Currently assigned to another template
                                                    </span>
                                                @endif
                                            </span>

                                            <span class="course-check-icon mt-1 text-xs text-brand-600">
                                                <i class="fa-solid fa-check"></i>
                                            </span>
                                        </label>
                                    @empty
                                        <div class="px-4 py-12 text-center">
                                            <i class="fa-solid fa-book-open text-2xl text-slate-300"></i>
                                            <p class="mt-3 text-sm font-semibold text-slate-600">
                                                No courses available
                                            </p>
                                            <p class="mt-1 text-xs text-slate-400">
                                                Create a course before assigning a certificate template.
                                            </p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>

                            <div class="mt-3 flex items-start gap-2 text-xs leading-5 text-slate-500">
                                <i class="fa-solid fa-circle-info mt-0.5 text-brand-500"></i>
                                <p>
                                    A course can use one template at a time. Assigning a course to this template will replace its previous template assignment when you save.
                                </p>
                            </div>

                            @error('course_ids')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror

                            @error('course_ids.*')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </section>
                </div>

                {{-- Settings column --}}
                <div class="space-y-6 xl:col-span-4">

                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-100 px-5 py-4">
                            <h3 class="font-semibold text-brand-950">Template Settings</h3>
                            <p class="mt-1 text-xs text-slate-500">
                                Configure visibility and display order.
                            </p>
                        </div>

                        <div class="space-y-5 p-5">
                            <div>
                                <label for="sort_order" class="mb-2 block text-sm font-semibold text-slate-700">
                                    Display Order
                                </label>

                                <input
                                    type="number"
                                    id="sort_order"
                                    name="sort_order"
                                    min="0"
                                    max="999999"
                                    required
                                    value="{{ old('sort_order', $template->sort_order ?? 0) }}"
                                    class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"
                                >

                                @error('sort_order')
                                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="rounded-xl border border-slate-200 p-4">
                                <input type="hidden" name="is_active" value="0">

                                <label for="is_active" class="flex cursor-pointer items-start gap-3">
                                    <input
                                        type="checkbox"
                                        id="is_active"
                                        name="is_active"
                                        value="1"
                                        class="mt-1 h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                                        @checked((bool) old('is_active', $template->exists ? $template->is_active : true))
                                    >

                                    <span>
                                        <span class="block text-sm font-semibold text-slate-700">
                                            Active Template
                                        </span>
                                        <span class="mt-1 block text-xs leading-5 text-slate-500">
                                            Allow this template to be used for new course assignments and certificate issuance.
                                        </span>
                                    </span>
                                </label>
                            </div>

                            @if($isEditing)
                                <div class="rounded-xl bg-slate-50 p-4">
                                    <p class="text-xs font-medium text-slate-500">Template ID</p>
                                    <p class="mt-1 text-lg font-bold text-brand-950">
                                        #{{ $template->id }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- Save actions --}}
                    <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <button
                            type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700"
                        >
                            <i class="fa-solid fa-floppy-disk"></i>
                            {{ $isEditing ? 'Save Changes' : 'Create Template' }}
                        </button>

                        <a
                            href="{{ route('admin.certificates.index') }}"
                            class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                        >
                            Cancel
                        </a>

                        <p class="mt-4 text-center text-xs leading-5 text-slate-400">
                            Review the template details and course assignments before saving.
                        </p>
                    </section>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('courseSearch');
            const courseOptions = Array.from(document.querySelectorAll('.course-option'));
            const courseCheckboxes = Array.from(document.querySelectorAll('.course-checkbox'));
            const selectedCount = document.getElementById('selectedCourseCount');
            const toggleVisibleButton = document.getElementById('toggleVisibleCourses');

            const fileInput = document.getElementById('background_image');
            const fileName = document.getElementById('selectedFileName');
            const newImagePreview = document.getElementById('newImagePreview');
            const newImagePreviewImage = document.getElementById('newImagePreviewImage');

            const certificateBackground = document.getElementById('certificatePreviewBackground');
            const previewPlaceholder = document.getElementById('previewPlaceholder');
            const fieldCheckboxes = Array.from(document.querySelectorAll('.certificate-field'));

         const previewFields = {
    company_name: document.querySelector('[data-field="company_name"]'),
    student_name: document.getElementById('previewStudentName'),
    course_name: document.getElementById('previewCourseName'),
    completion_date: document.getElementById('previewCompletionDate'),
    certificate_number: document.querySelector('[data-field="certificate_number"]'),
    signer_name: document.querySelector('[data-field="signer_name"]'),
    signer_designation: document.querySelector('[data-field="signer_designation"]'),
};

            function updateCourseCount() {
                const count = courseCheckboxes.filter(input => input.checked).length;
                selectedCount.textContent = count + ' selected';

                courseOptions.forEach(function (option) {
                    const checkbox = option.querySelector('.course-checkbox');
                    const icon = option.querySelector('.course-check-icon');

                    if (checkbox && icon) {
                        icon.classList.toggle('invisible', !checkbox.checked);
                    }
                });

                const visibleCheckboxes = courseOptions
                    .filter(option => option.style.display !== 'none')
                    .map(option => option.querySelector('.course-checkbox'))
                    .filter(Boolean);

                const allVisibleSelected = visibleCheckboxes.length > 0 &&
                    visibleCheckboxes.every(input => input.checked);

                toggleVisibleButton.textContent = allVisibleSelected
                    ? 'Unselect visible'
                    : 'Select visible';
            }

            function updateCertificateFields() {
                const selected = fieldCheckboxes
                    .filter(input => input.checked)
                    .map(input => input.value);

                Object.entries(previewFields).forEach(function ([field, element]) {
                    if (element) {
                        element.classList.toggle('hidden', !selected.includes(field));
                    }
                });
            }

            searchInput.addEventListener('input', function () {
                const query = this.value.trim().toLowerCase();

                courseOptions.forEach(function (option) {
                    const title = option.dataset.courseTitle || '';
                    option.style.display = title.includes(query) ? 'flex' : 'none';
                });

                updateCourseCount();
            });

            courseCheckboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', updateCourseCount);
            });

            toggleVisibleButton.addEventListener('click', function () {
                const visibleCheckboxes = courseOptions
                    .filter(option => option.style.display !== 'none')
                    .map(option => option.querySelector('.course-checkbox'))
                    .filter(Boolean);

                const allSelected = visibleCheckboxes.length > 0 &&
                    visibleCheckboxes.every(input => input.checked);

                visibleCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = !allSelected;
                });

                updateCourseCount();
            });

            fieldCheckboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', updateCertificateFields);
            });

            fileInput.addEventListener('change', function () {
                const file = this.files && this.files[0];

                if (!file) {
                    fileName.textContent = '';
                    newImagePreview.classList.add('hidden');
                    newImagePreviewImage.removeAttribute('src');
                    return;
                }

                fileName.textContent = 'Selected file: ' + file.name;

                if (!file.type.startsWith('image/')) {
                    newImagePreview.classList.add('hidden');
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    const imageUrl = event.target.result;

                    newImagePreviewImage.src = imageUrl;
                    newImagePreview.classList.remove('hidden');

                    if (certificateBackground) {
                        certificateBackground.src = imageUrl;
                        certificateBackground.classList.remove('hidden');
                    }

                    if (previewPlaceholder) {
                        previewPlaceholder.classList.add('hidden');
                    }
                };

                reader.readAsDataURL(file);
            });

            updateCourseCount();
            updateCertificateFields();
        });
    </script>

</x-layouts.admin>
