<x-layouts.admin :title="$template->exists ? 'Edit Certificate Template' : 'Create Certificate Template'">

    @php
        $selectedFields = old('fields', $template->fields ?? [
            'student_name',
            'course_name',
            'completion_date',
            'certificate_number',
        ]);

        $selectedCourses = collect(old('course_ids', $assignedCourseIds ?? []))
            ->map(fn ($id) => (int) $id)
            ->all();

        $isEditing = $template->exists;

     $availableFields = [
    'company_name' => 'Company / Institute Name',
    'student_name' => 'Student Name',
    'course_name' => 'Course Name',
    'completion_date' => 'Completion Date',
    'certificate_number' => 'Certificate Number',
    'signer_name' => 'Manager / Signatory Name',
    'signer_designation' => 'Signatory Designation',
];
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

        {{-- Success and error messages --}}
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

        @if($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4">
                <div class="flex items-center gap-2 text-sm font-semibold text-rose-700">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    Please fix the following errors.
                </div>

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

                            {{-- IMPORTANT: database column is title, not name --}}
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

                            {{-- Background image --}}
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

                                @if($template->background_image)
                                    <div class="mt-4">
                                        <p class="mb-2 text-sm font-semibold text-slate-700">
                                            Current Background
                                        </p>

                                        <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50 p-2">
                                            <img
                                                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($template->background_image) }}"
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
                                            class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
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
                                                <span class="course-title block text-sm font-semibold text-slate-700">
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

    {{-- Page interactions --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('courseSearch');
            const courseOptions = Array.from(document.querySelectorAll('.course-option'));
            const courseCheckboxes = Array.from(document.querySelectorAll('.course-checkbox'));
            const selectedCount = document.getElementById('selectedCourseCount');
            const toggleVisibleButton = document.getElementById('toggleVisibleCourses');
            const fileInput = document.getElementById('background_image');
            const fileName = document.getElementById('selectedFileName');
            const previewContainer = document.getElementById('newImagePreview');
            const previewImage = document.getElementById('newImagePreviewImage');

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

            fileInput.addEventListener('change', function () {
                const file = this.files && this.files[0];

                if (!file) {
                    fileName.textContent = '';
                    previewContainer.classList.add('hidden');
                    previewImage.removeAttribute('src');
                    return;
                }

                fileName.textContent = 'Selected file: ' + file.name;

                if (!file.type.startsWith('image/')) {
                    previewContainer.classList.add('hidden');
                    previewImage.removeAttribute('src');
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    previewImage.src = event.target.result;
                    previewContainer.classList.remove('hidden');
                };

                reader.readAsDataURL(file);
            });

            updateCourseCount();
        });
    </script>

</x-layouts.admin>
