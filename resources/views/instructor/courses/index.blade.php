<x-layouts.instructor title="My Courses">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-brand-950">My Courses</h2>
            <p class="text-sm text-slate-500 mt-1">Courses you've created. New ones need admin approval before they go live.</p>
        </div>
        <a href="{{ route('instructor.courses.create') }}"
            class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-accent-600/30 hover:shadow-lg transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            New Course
        </a>
    </div>

    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <x-admin.search-input :value="$search" placeholder="Search your courses..." />
        <select name="status" onchange="this.form.submit()"
            class="rounded-full bg-white border border-slate-200 px-4 py-2.5 text-sm text-slate-600 focus:ring-2 focus:ring-brand-200 outline-none">
            <option value="all" @selected($selectedStatus == 'all')>All Status</option>
            <option value="draft" @selected($selectedStatus == 'draft')>Draft</option>
            <option value="pending" @selected($selectedStatus == 'pending')>Pending Review</option>
            <option value="active" @selected($selectedStatus == 'active')>Live</option>
            <option value="rejected" @selected($selectedStatus == 'rejected')>Rejected</option>
        </select>
        <button type="submit" class="rounded-full bg-brand-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-950 transition">Filter</button>
    </form>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-50/60 text-left text-sm font-semibold uppercase tracking-wide text-brand-700">
                        <th class="px-5 py-3">Course</th>
                        <th class="px-5 py-3">Content</th>
                        <th class="px-5 py-3">Students</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($courses as $course)
                        <tr class="hover:bg-brand-50/30 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="h-11 w-11 flex-shrink-0 rounded-xl bg-gradient-to-br from-brand-700 to-brand-900 flex items-center justify-center overflow-hidden">
                                        @if ($course->thumbnail)
                                            <img src="{{ Storage::url($course->thumbnail) }}" class="h-full w-full object-cover">
                                        @else
                                            <i class="fa-solid fa-book-open text-white text-sm"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-brand-950 truncate max-w-xs" title="{{ $course->title }}">{{ $course->title }}</p>
                                        @if ($course->status === 'rejected' && $course->rejection_reason)
                                            <p class="text-xs text-rose-500 truncate max-w-xs" title="{{ $course->rejection_reason }}">{{ $course->rejection_reason }}</p>
                                        @else
                                            <p class="text-xs text-slate-400">{{ ucfirst(str_replace('_', ' ', $course->level)) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 text-xs">
                                <p>{{ $course->sections_count }} Section{{ $course->sections_count === 1 ? '' : 's' }}</p>
                                <p>{{ $course->lessons_count }} Lesson{{ $course->lessons_count === 1 ? '' : 's' }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 text-slate-600">
                                    <i class="fa-solid fa-user-graduate text-slate-400 text-xs"></i>
                                    {{ $course->enrollments_count }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="rounded-full px-3 py-1 text-xs font-semibold
                                    @switch($course->status)
                                        @case('active') bg-emerald-50 text-emerald-700 @break
                                        @case('pending') bg-amber-50 text-amber-700 @break
                                        @case('rejected') bg-rose-50 text-rose-700 @break
                                        @default bg-slate-100 text-slate-600
                                    @endswitch">
                                    {{ $course->status === 'active' ? 'Live' : ucfirst($course->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('instructor.courses.builder', $course) }}" class="h-8 w-8 rounded-lg bg-brand-50 text-brand-700 hover:bg-brand-100 flex items-center justify-center" title="Manage Content">
                                        <i class="fa-solid fa-layer-group text-xs"></i>
                                    </a>
                                    <a href="{{ route('instructor.courses.edit', $course) }}" class="h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center" title="Edit">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('instructor.courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Delete this course? This also removes its sections, lessons and enrollments.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="h-8 w-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center" title="Delete">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center text-slate-400">
                                <i class="fa-solid fa-book-open text-2xl mb-2 block"></i>
                                No courses yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-admin.pagination :paginator="$courses" />
</x-layouts.instructor>
