<x-layouts.admin title="Category Details">
    {{-- Welcome banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-brand-100 px-6 sm:px-8 py-5 mb-8">
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-4 min-w-0">
                <a href="{{ route('admin.categories.index') }}" class="h-10 w-10 flex-shrink-0 rounded-full bg-white/80 text-brand-700 hover:bg-white flex items-center justify-center shadow-sm transition">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div class="h-16 w-16 flex-shrink-0 rounded-2xl bg-accent-500 shadow-lg flex items-center justify-center">
                    <i class="{{ $category->icon ?: 'fa-solid fa-shapes' }} text-white text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-950 tracking-tight truncate">{{ $category->name }}</h2>
                    <p class="mt-1 text-sm font-semibold text-accent-600">
                        @if ($category->parent)
                            Sub-category of {{ $category->parent->name }}
                        @else
                            Top-level category &middot; Code: {{ $category->code }}
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex-shrink-0">
                <x-admin.badge :color="$category->status ? 'emerald' : 'rose'">{{ $category->status ? 'Active' : 'Inactive' }}</x-admin.badge>
            </div>
        </div>

        <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-brand-200/50 blur-2xl"></div>
        <div class="pointer-events-none absolute right-24 bottom-0 h-28 w-28 rounded-full bg-accent-400/20 blur-2xl"></div>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        <x-admin.info-card icon="fa-folder-tree" color="violet" label="Sub-categories">
            {{ $category->children->count() }}
        </x-admin.info-card>
        <x-admin.info-card icon="fa-book-open" color="sky" label="Courses">
            {{ $courses->count() }}
        </x-admin.info-card>
        <x-admin.info-card icon="fa-user-graduate" color="emerald" label="Total Enrollments">
            {{ $courses->sum('enrollments_count') }}
        </x-admin.info-card>
    </div>

    {{-- Sub-categories --}}
    @if ($category->children->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4">
                <span class="h-7 w-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-folder-tree text-xs"></i>
                </span>
                <h3 class="text-lg font-bold text-brand-950">Sub-categories</h3>
            </div>
            <div class="flex flex-wrap gap-3">
                @foreach ($category->children as $child)
                    <a href="{{ route('admin.categories.show', $child) }}"
                        class="inline-flex items-center gap-2 rounded-full bg-white border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:border-brand-300 hover:text-brand-700 hover:shadow-sm transition">
                        <i class="{{ $child->icon ?: 'fa-solid fa-circle-small' }} text-brand-400 text-xs"></i>
                        {{ $child->name }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Courses in this category --}}
    <div>
        <div class="flex items-center gap-2 mb-4">
            <span class="h-7 w-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-book-open text-xs"></i>
            </span>
            <h3 class="text-lg font-bold text-brand-950">Courses in This Category</h3>
        </div>

        @if ($courses->isEmpty())
            <x-admin.empty-state icon="fa-book-open" title="No courses yet" description="No courses have been added to this category." />
        @else
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-brand-50/60 text-left text-sm font-semibold uppercase tracking-wide text-brand-700">
                                <th class="px-5 py-3">Course</th>
                                <th class="px-5 py-3">Instructor</th>
                                <th class="px-5 py-3">Category</th>
                                <th class="px-5 py-3">Sub-category</th>
                                <th class="px-5 py-3">Students</th>
                                <th class="px-5 py-3">Price</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($courses as $course)
                                <tr class="hover:bg-brand-50/30 transition">
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('admin.courses.show', $course) }}" class="flex items-center gap-3 group">
                                            <div class="h-10 w-10 flex-shrink-0 rounded-xl bg-brand-500 flex items-center justify-center overflow-hidden">
                                                @if ($course->thumbnail)
                                                    <img src="{{ Storage::url($course->thumbnail) }}" class="h-full w-full object-cover">
                                                @else
                                                    <i class="fa-solid fa-book-open text-white text-sm"></i>
                                                @endif
                                            </div>
                                            <span class="text-sm font-semibold text-brand-950 group-hover:text-brand-700 truncate max-w-xs" title="{{ $course->title }}">{{ $course->title }}</span>
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600">{{ $course->instructor->name ?? '—' }}</td>
                                    <td class="px-5 py-3.5">
                                        <x-admin.badge color="brand">{{ $course->category->name ?? '—' }}</x-admin.badge>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if ($course->subCategory)
                                            <x-admin.badge color="accent">{{ $course->subCategory->name }}</x-admin.badge>
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 text-slate-600">
                                            <i class="fa-solid fa-user-graduate text-slate-400 text-xs"></i>
                                            {{ $course->enrollments_count }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600">
                                        @if ($course->is_free)
                                            <span class="text-emerald-600 font-semibold">Free</span>
                                        @else
                                            {{ format_price($course->price) }}
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <x-admin.badge :color="$course->statusColor()">{{ ucfirst($course->status) }}</x-admin.badge>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
