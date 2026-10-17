<x-layouts.admin title="Enrollment History">
    {{-- Welcome banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-brand-100 px-6 sm:px-8 py-5 mb-8">
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
            <div class="max-w-lg">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-950 tracking-tight">Enrollment History</h2>
                <p class="mt-1 text-sm font-semibold text-accent-600">Every student enrollment across all courses.</p>
            </div>

            <div class="flex items-center gap-8">
                {{-- Decorative cluster --}}
                <div class="hidden lg:block relative w-20 h-20 flex-shrink-0">
                    <div class="h-20 w-20 rounded-3xl bg-brand-500 rotate-6 shadow-xl flex items-center justify-center">
                        <i class="fa-solid fa-user-graduate text-3xl text-accent-400 -rotate-6"></i>
                    </div>
                    <div class="absolute -bottom-3 -left-4 h-10 w-14 rounded-xl bg-accent-500 shadow-lg flex items-center justify-center -rotate-12">
                        <i class="fa-solid fa-book-open text-base text-white"></i>
                    </div>
                </div>

                <a href="{{ route('admin.enrollments.create') }}"
                    class="flex-shrink-0 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-accent-600/30 hover:shadow-xl hover:-translate-y-0.5 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Enroll Student
                </a>
            </div>
        </div>

        <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-brand-200/50 blur-2xl"></div>
        <div class="pointer-events-none absolute right-24 bottom-0 h-28 w-28 rounded-full bg-accent-400/20 blur-2xl"></div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <x-admin.stat-card
            icon="fa-clipboard-list"
            color="violet"
            :count="$stats['total']['count']"
            label="Total Enrollments"
            :change="$stats['total']['change']"
            :direction="$stats['total']['direction']"
        />
        <x-admin.stat-card
            icon="fa-calendar-check"
            color="emerald"
            :count="$stats['thisMonth']['count']"
            label="Enrolled This Month"
            :change="$stats['thisMonth']['change']"
            :direction="$stats['thisMonth']['direction']"
        />
        <x-admin.stat-card
            icon="fa-users"
            color="sky"
            :count="$stats['students']['count']"
            label="Students Enrolled"
            :change="$stats['students']['change']"
            :direction="$stats['students']['direction']"
        />
        <x-admin.stat-card
            icon="fa-chalkboard-user"
            color="amber"
            :count="$stats['courses']['count']"
            label="Courses With Students"
            :change="$stats['courses']['change']"
            :direction="$stats['courses']['direction']"
        />
    </div>

    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <x-admin.search-input :value="$search" placeholder="Search student..." />
        <x-admin.searchable-select
            name="course_id"
            :options="$courses->pluck('title', 'id')"
            :selected="$selectedCourse"
            all-label="All Courses"
            pill
            autosubmit
            class="w-56"
        />
        <x-admin.searchable-select
            name="price_filter"
            :options="['free' => 'Free', 'paid' => 'Paid']"
            :selected="$selectedPriceFilter"
            all-label="All Prices"
            pill
            autosubmit
            class="w-40"
        />
        <div class="flex items-center gap-2 rounded-full bg-white border border-slate-200 px-4 py-2 text-sm text-slate-500">
            <i class="fa-regular fa-calendar text-xs text-slate-400"></i>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="outline-none text-slate-600 bg-transparent">
            <span class="text-slate-300">–</span>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="outline-none text-slate-600 bg-transparent">
        </div>
        <button type="submit" class="rounded-full bg-brand-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-950 transition">Filter</button>
        @if ($search || $selectedCourse !== 'all' || $selectedPriceFilter !== 'all' || $dateFrom || $dateTo)
            <a href="{{ route('admin.enrollments.index') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700">Clear</a>
        @endif
    </form>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-50/60 text-left text-sm font-semibold uppercase tracking-wide text-brand-700">
                        <th class="px-5 py-3">Student</th>
                        <th class="px-5 py-3">Enrolled Course</th>
                        <th class="px-5 py-3">Price</th>
                        <th class="px-5 py-3">Enrolled On</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($enrollments as $enrollment)
                        <tr class="hover:bg-brand-50/30 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-brand-900 text-white flex items-center justify-center font-semibold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($enrollment->user->name ?? '?', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-brand-950 truncate">{{ $enrollment->user->name ?? '—' }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ $enrollment->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $enrollment->course->title ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                @if (! $enrollment->course)
                                    <span class="text-slate-400">—</span>
                                @elseif ($enrollment->course->is_free)
                                    <span class="text-xs font-semibold text-emerald-600">Free</span>
                                @elseif ($enrollment->course->hasDiscount())
                                    <span class="text-slate-700 font-medium">{{ format_price($enrollment->course->discount_price) }}</span>
                                    <span class="text-xs text-slate-400 line-through ml-1">{{ format_price($enrollment->course->price) }}</span>
                                @else
                                    <span class="text-slate-700 font-medium">{{ format_price($enrollment->course->price) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $enrollment->created_at->format('D, d M Y') }}</td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.enrollments.show', $enrollment) }}" class="h-8 w-8 rounded-lg bg-brand-50 text-brand-700 hover:bg-brand-100 flex items-center justify-center" title="Details">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.enrollments.edit', $enrollment) }}" class="h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center" title="Edit">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST" onsubmit="return confirm('Remove this enrollment?');">
                                        @csrf @method('DELETE')
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
