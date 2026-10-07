<x-layouts.admin title="Enrollment History">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-brand-950">Enrollment History</h2>
        <p class="text-sm text-slate-500 mt-1">Every student enrollment across all courses.</p>
    </div>

    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <x-admin.search-input :value="$search" placeholder="Search student..." />
        <select name="course_id" onchange="this.form.submit()" class="rounded-full bg-white border border-slate-200 px-4 py-2.5 text-sm text-slate-600 focus:ring-2 focus:ring-brand-200 outline-none max-w-xs">
            <option value="all" @selected($selectedCourse == 'all')>All Courses</option>
            @foreach ($courses as $c)
                <option value="{{ $c->id }}" @selected($selectedCourse == $c->id)>{{ $c->title }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-full bg-brand-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-950 transition">Filter</button>
    </form>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-50/60 text-left text-sm font-semibold uppercase tracking-wide text-brand-700">
                        <th class="px-5 py-3">Student</th>
                        <th class="px-5 py-3">Course</th>
                        <th class="px-5 py-3">Enrolled On</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($enrollments as $enrollment)
                        <tr class="hover:bg-brand-50/30 transition">
                            <td class="px-5 py-3.5">
                                <p class="text-lg font-semibold text-brand-950">{{ $enrollment->user->name ?? '—' }}</p>
                                <p class="text-xs text-slate-400">{{ $enrollment->user->email ?? '' }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $enrollment->course->title ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $enrollment->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-16 text-center text-slate-400">
                                <i class="fa-solid fa-user-graduate text-2xl mb-2 block"></i>
                                No enrollments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-admin.pagination :paginator="$enrollments" />
</x-layouts.admin>
