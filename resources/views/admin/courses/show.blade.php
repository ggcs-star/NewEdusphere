<x-layouts.admin title="Course Details">
    {{-- Welcome banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-brand-100 px-6 sm:px-8 py-5 mb-8">
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-4 min-w-0 flex-1">
                <a href="{{ route('admin.courses.index') }}" class="h-10 w-10 flex-shrink-0 rounded-full bg-white/80 text-brand-700 hover:bg-white flex items-center justify-center shadow-sm transition">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div class="h-16 w-16 flex-shrink-0 rounded-2xl bg-brand-500 shadow-lg flex items-center justify-center overflow-hidden">
                    @if ($course->thumbnail)
                        <img src="{{ Storage::url($course->thumbnail) }}" class="h-full w-full object-cover">
                    @else
                        <i class="fa-solid fa-book-open text-2xl text-white"></i>
                    @endif
                </div>
                <div class="min-w-0">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-950 tracking-tight truncate" title="{{ $course->title }}">{{ $course->title }}</h2>
                    <p class="mt-1 text-sm font-semibold text-accent-600">by {{ $course->instructor->name ?? '—' }} &middot; {{ $course->category->name ?? '—' }}</p>
                </div>
            </div>

            <div class="flex flex-shrink-0 items-center gap-2">
                <a href="{{ route('admin.courses.builder', $course) }}"
                    class="inline-flex items-center gap-2 rounded-full bg-white/90 px-5 py-2.5 text-sm font-semibold text-brand-700 shadow-sm hover:bg-white transition">
                    <i class="fa-solid fa-layer-group text-xs"></i> Manage Content
                </a>
                <a href="{{ route('admin.courses.edit', $course) }}"
                    class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-accent-600/30 hover:shadow-xl hover:-translate-y-0.5 transition">
                    <i class="fa-solid fa-pen text-xs"></i> Edit
                </a>
            </div>
        </div>

        <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-brand-200/50 blur-2xl"></div>
        <div class="pointer-events-none absolute right-24 bottom-0 h-28 w-28 rounded-full bg-accent-400/20 blur-2xl"></div>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <x-admin.info-card icon="fa-circle-check" color="{{ $course->statusColor() === 'rose' ? 'rose' : ($course->statusColor() === 'amber' ? 'amber' : 'emerald') }}" label="Status">
            {{ ucfirst($course->status) }}
        </x-admin.info-card>
        <x-admin.info-card icon="fa-layer-group" color="violet" label="{{ $course->lessons_count }} Lessons">
            {{ $course->sections_count }} Sections
        </x-admin.info-card>
        <x-admin.info-card icon="fa-user-graduate" color="sky" label="Students Enrolled">
            {{ $students->count() }}
        </x-admin.info-card>
        <x-admin.info-card icon="fa-tag" color="amber" label="Price">
            @if ($course->is_free)
                Free
            @elseif ($course->hasDiscount())
                <span class="text-base">{{ format_price($course->discount_price) }}</span>
                <span class="text-sm font-medium text-slate-400 line-through ml-1">{{ format_price($course->price) }}</span>
            @else
                {{ format_price($course->price) }}
            @endif
        </x-admin.info-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[7fr_3fr] gap-6 mb-8">
        {{-- Description --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 flex flex-col">
            <div class="flex items-center gap-2 mb-3">
                <span class="h-7 w-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-file-lines text-xs"></i>
                </span>
                <h3 class="text-base font-bold text-brand-950">Description</h3>
            </div>

            @if ($course->description)
                <div class="text-sm text-slate-600 leading-relaxed [&_p]:mb-3 [&_h3]:font-bold [&_h3]:text-brand-950 [&_h3]:mt-4 [&_h3]:mb-2 [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:space-y-1 [&_ul]:mb-3 [&_strong]:font-semibold [&_strong]:text-brand-950">
                    {!! $course->description !!}
                </div>
            @else
                <p class="text-sm text-slate-500">{{ $course->short_description ?: 'No description provided.' }}</p>
            @endif
        </div>

        {{-- Meta --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-3">
                <span class="h-7 w-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-sliders text-xs"></i>
                </span>
                <h3 class="text-base font-bold text-brand-950">Course Details</h3>
            </div>
            <dl class="divide-y divide-slate-100 text-sm">
                <div class="flex justify-between gap-3 py-2.5">
                    <dt class="text-slate-400">Level</dt>
                    <dd class="font-medium text-slate-700">{{ ucfirst(str_replace('_', ' ', $course->level)) }}</dd>
                </div>
                <div class="flex justify-between gap-3 py-2.5">
                    <dt class="text-slate-400">Language</dt>
                    <dd class="font-medium text-slate-700">{{ $course->language }}</dd>
                </div>
                <div class="flex justify-between gap-3 py-2.5">
                    <dt class="text-slate-400">Category</dt>
                    <dd class="font-medium text-slate-700">{{ $course->category->name ?? '—' }}</dd>
                </div>
                @if ($course->subCategory)
                    <div class="flex justify-between gap-3 py-2.5">
                        <dt class="text-slate-400">Sub-category</dt>
                        <dd class="font-medium text-slate-700">{{ $course->subCategory->name }}</dd>
                    </div>
                @endif
                <div class="flex justify-between gap-3 py-2.5">
                    <dt class="text-slate-400">Top Course</dt>
                    <dd class="font-medium text-slate-700">{{ $course->is_top_course ? 'Yes' : 'No' }}</dd>
                </div>
                <div class="flex justify-between gap-3 py-2.5">
                    <dt class="text-slate-400">Created</dt>
                    <dd class="font-medium text-slate-700">{{ $course->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    @if (!empty($course->outcomes) || !empty($course->requirements))
        {{-- Learning outcomes & requirements --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:divide-x md:divide-slate-100">
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="h-7 w-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-lightbulb text-xs"></i>
                        </span>
                        <h4 class="text-sm font-bold text-brand-950">What students will learn</h4>
                    </div>
                    @if (!empty($course->outcomes))
                        <ul class="space-y-2">
                            @foreach ($course->outcomes as $outcome)
                                <li class="flex items-start gap-2.5 text-sm text-slate-600">
                                    <span class="mt-0.5 h-4 w-4 rounded-full bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-check text-[9px]"></i>
                                    </span>
                                    {{ $outcome }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-slate-400">Not specified.</p>
                    @endif
                </div>

                <div class="md:pl-8">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="h-7 w-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-clipboard-list text-xs"></i>
                        </span>
                        <h4 class="text-sm font-bold text-brand-950">Requirements</h4>
                    </div>
                    @if (!empty($course->requirements))
                        <ul class="space-y-1.5">
                            @foreach ($course->requirements as $requirement)
                                <li class="flex items-start gap-2 text-sm text-slate-600">
                                    <i class="fa-solid fa-circle text-[4px] text-slate-400 mt-2"></i>{{ $requirement }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-slate-400">Not specified.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Students who took the course --}}
    <div>
        <div class="flex items-center gap-2 mb-4">
            <span class="h-7 w-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-user-graduate text-xs"></i>
            </span>
            <h3 class="text-lg font-bold text-brand-950">Students Enrolled in This Course</h3>
        </div>

        @if ($students->isEmpty())
            <x-admin.empty-state icon="fa-user-graduate" title="No enrollments yet" description="No one has taken this course yet." />
        @else
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-brand-50/60 text-left text-sm font-semibold uppercase tracking-wide text-brand-700">
                                <th class="px-5 py-3">Student</th>
                                <th class="px-5 py-3">Email</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($students as $student)
                                <tr class="hover:bg-brand-50/30 transition">
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('admin.users.show', $student) }}" class="flex items-center gap-3 group">
                                            <div class="h-9 w-9 rounded-full bg-brand-900 text-white flex items-center justify-center font-semibold text-xs flex-shrink-0">
                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm font-semibold text-brand-950 group-hover:text-brand-700">{{ $student->name }}</span>
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-600">{{ $student->email }}</td>
                                    <td class="px-5 py-3.5">
                                        <x-admin.badge :color="$student->status ? 'emerald' : 'rose'">
                                            <span class="h-1.5 w-1.5 rounded-full {{ $student->status ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                            {{ $student->status ? 'Active' : 'Suspended' }}
                                        </x-admin.badge>
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
