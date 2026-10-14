@php $isEdit = $enrollment->exists; @endphp
<x-layouts.admin :title="$isEdit ? 'Edit Enrollment' : 'Enroll Student'">
    <div
        x-data="{
            userId: '{{ old('user_id', $enrollment->user_id) }}',
            courseId: '{{ old('course_id', $enrollment->course_id) }}',
            courses: @js($courses->map(fn ($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'short_description' => $c->short_description,
                'category' => $c->category->name ?? null,
                'subCategory' => $c->subCategory->name ?? null,
                'instructor' => $c->instructor->name ?? null,
                'thumbnail' => $c->thumbnail ? Storage::url($c->thumbnail) : null,
                'is_free' => $c->is_free,
                'price' => (float) $c->price,
                'discount_price' => $c->discount_price !== null ? (float) $c->discount_price : null,
                'level' => $c->level,
                'language' => $c->language,
                'status' => $c->status,
            ])),
            get selectedCourse() { return this.courses.find(c => c.id == this.courseId) ?? null; },
            get coursePriceLabel() {
                if (!this.selectedCourse) return '';
                if (this.selectedCourse.is_free) return 'Free';
                return '$' + Number(this.selectedCourse.discount_price ?? this.selectedCourse.price).toFixed(2);
            },
            get courseOriginalPriceLabel() {
                if (!this.selectedCourse || this.selectedCourse.is_free || this.selectedCourse.discount_price === null) return null;
                return '$' + Number(this.selectedCourse.price).toFixed(2);
            },
            formatLevel(level) { return (level ?? '').replaceAll('_', ' '); }
        }"
    >
        {{-- Banner header --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-brand-100 px-6 sm:px-8 py-5 mb-8">
            <div class="relative z-10 flex flex-wrap items-center gap-4">
                <a href="{{ route('admin.enrollments.index') }}" class="h-10 w-10 flex-shrink-0 rounded-full bg-white/80 text-brand-700 hover:bg-white flex items-center justify-center shadow-sm transition">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div class="hidden sm:flex h-14 w-14 flex-shrink-0 rounded-2xl bg-brand-500 shadow-lg items-center justify-center">
                    <i class="fa-solid fa-user-graduate text-2xl text-accent-400"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-950 tracking-tight">{{ $isEdit ? 'Edit Enrollment' : 'Enroll Student' }}</h2>
                    <p class="mt-1 text-sm font-semibold text-accent-600">{{ $isEdit ? 'Reassign this enrollment to a different student or course.' : 'Manually enroll a student into a course.' }}</p>
                </div>
            </div>

            <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-brand-200/50 blur-2xl"></div>
            <div class="pointer-events-none absolute right-24 bottom-0 h-28 w-28 rounded-full bg-accent-400/20 blur-2xl"></div>
        </div>

        <form action="{{ $isEdit ? route('admin.enrollments.update', $enrollment) : route('admin.enrollments.store') }}" method="POST">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="user_id" class="flex items-center gap-2 text-sm font-semibold text-brand-950 mb-1.5">
                        <i class="fa-solid fa-user-graduate text-brand-400 text-xs"></i> Student <span class="text-rose-500">*</span>
                    </label>
                    <x-admin.searchable-select
                        id="user_id"
                        name="user_id"
                        :options="$users->mapWithKeys(fn ($u) => [$u->id => $u->name.' ('.$u->email.')'])"
                        placeholder="Select a student"
                        bind-to="userId"
                        required
                    />
                </div>

                <div>
                    <label for="course_id" class="flex items-center gap-2 text-sm font-semibold text-brand-950 mb-1.5">
                        <i class="fa-solid fa-book-open text-brand-400 text-xs"></i> Course <span class="text-rose-500">*</span>
                    </label>
                    <x-admin.searchable-select
                        id="course_id"
                        name="course_id"
                        :options="$courses->pluck('title', 'id')"
                        placeholder="Select a course"
                        bind-to="courseId"
                        required
                    />
                </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <a href="{{ route('admin.enrollments.index') }}" class="rounded-lg px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100">Cancel</a>
                <button type="submit" class="rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-accent-600/30 hover:shadow-lg transition">
                    {{ $isEdit ? 'Save Changes' : 'Enroll Student' }}
                </button>
            </div>
        </form>

        {{-- Course details — updates to whichever course is selected above --}}
        <div class="mt-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="h-7 w-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-book-open text-xs"></i>
                </span>
                <h3 class="text-lg font-bold text-brand-950">Course Details</h3>
            </div>

            <template x-if="!selectedCourse">
                <x-admin.empty-state icon="fa-book-open" title="No course selected" description="Choose a course above to see its full details here." />
            </template>

            <template x-if="selectedCourse">
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
                    <div class="flex flex-col sm:flex-row gap-5">
                        <div class="h-24 w-24 flex-shrink-0 rounded-xl overflow-hidden bg-brand-500 flex items-center justify-center">
                            <img x-show="selectedCourse.thumbnail" :src="selectedCourse.thumbnail" class="h-full w-full object-cover" alt="">
                            <i x-show="!selectedCourse.thumbnail" class="fa-solid fa-book-open text-white text-2xl"></i>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-base font-bold text-brand-950" x-text="selectedCourse.title"></p>
                                    <p class="text-sm text-slate-500 mt-1" x-text="selectedCourse.short_description || 'No description provided.'"></p>
                                </div>
                                <div class="flex-shrink-0 text-right">
                                    <p class="text-lg font-extrabold" :class="selectedCourse.is_free ? 'text-emerald-600' : 'text-brand-950'" x-text="coursePriceLabel"></p>
                                    <p x-show="courseOriginalPriceLabel" class="text-xs font-medium text-slate-400 line-through" x-text="courseOriginalPriceLabel"></p>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-1.5 mt-3">
                                <span class="inline-flex items-center rounded-full bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-700" x-text="selectedCourse.category ?? 'Uncategorized'"></span>
                                <span x-show="selectedCourse.subCategory" class="inline-flex items-center rounded-full bg-accent-50 px-2.5 py-1 text-xs font-semibold text-accent-700" x-text="selectedCourse.subCategory"></span>
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 capitalize" x-text="formatLevel(selectedCourse.level)"></span>
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 capitalize" x-text="selectedCourse.status"></span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-slate-100">
                                <div>
                                    <p class="text-xs text-slate-400">Instructor</p>
                                    <p class="text-sm font-semibold text-brand-950 truncate" x-text="selectedCourse.instructor ?? '—'"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400">Language</p>
                                    <p class="text-sm font-semibold text-brand-950" x-text="selectedCourse.language ?? '—'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</x-layouts.admin>
