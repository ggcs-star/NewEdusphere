<x-layouts.admin title="Dashboard">
    {{-- Welcome banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-brand-100 px-6 sm:px-8 py-5 mb-8">
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
            <div class="max-w-lg">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-950 tracking-tight">Welcome back, {{ current_admin_user()->name ?? 'Instructor' }}</h2>
                <p class="mt-1 text-sm font-semibold text-accent-600">Create courses, track reviews, and see how your students are growing.</p>
            </div>

            <a href="{{ route('instructor.courses.create') }}"
                class="flex-shrink-0 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-accent-600/30 hover:shadow-xl hover:-translate-y-0.5 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                New Course
            </a>
        </div>

        <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-brand-200/50 blur-2xl"></div>
        <div class="pointer-events-none absolute right-24 bottom-0 h-28 w-28 rounded-full bg-accent-400/20 blur-2xl"></div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
        <x-admin.stat-card icon="fa-book-open" color="violet" :count="$stats['total']" label="Total Courses" :change="0" direction="flat" :href="route('instructor.courses.index')" />
        <x-admin.stat-card icon="fa-circle-check" color="emerald" :count="$stats['active']" label="Live" :change="0" direction="flat" :href="route('instructor.courses.index', ['status' => 'active'])" />
        <x-admin.stat-card icon="fa-hourglass-half" color="amber" :count="$stats['pending']" label="Pending Review" :change="0" direction="flat" :href="route('instructor.courses.index', ['status' => 'pending'])" />
        <x-admin.stat-card icon="fa-circle-xmark" color="sky" :count="$stats['rejected']" label="Rejected" :change="0" direction="flat" :href="route('instructor.courses.index', ['status' => 'rejected'])" />
        <x-admin.stat-card icon="fa-user-graduate" color="violet" :count="$stats['students']" label="Students Enrolled" :change="0" direction="flat" />
    </div>

    <x-admin.revenue-analytics
        :analytics="$analytics"
        period-route="instructor.dashboard"
        revenue-route="instructor.revenue.index"
        courses-route="instructor.courses.index"
        :is-admin="false"
    />

    {{-- Recent courses --}}
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-brand-950">Recent Courses</h3>
            <a href="{{ route('instructor.courses.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">View all</a>
        </div>

        @if ($recentCourses->isEmpty())
            <x-admin.empty-state icon="fa-book-open" title="No courses yet" description="Create your first course to get started." />
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($recentCourses as $course)
                    @php
                        $statusColor = match ($course->status) {
                            'active' => 'emerald',
                            'pending' => 'amber',
                            'rejected' => 'rose',
                            default => 'slate',
                        };
                    @endphp
                    <div class="flex items-center justify-between py-3">
                        <div class="min-w-0">
                            <a href="{{ route('instructor.courses.edit', $course) }}" class="text-sm font-semibold text-brand-950 hover:text-brand-700 truncate block" title="{{ $course->title }}">{{ $course->title }}</a>
                            <p class="text-xs text-slate-400">{{ $course->enrollments_count }} student{{ $course->enrollments_count === 1 ? '' : 's' }}</p>
                        </div>
                        <x-admin.badge :color="$statusColor" class="flex-shrink-0">{{ ucfirst($course->status) }}</x-admin.badge>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.admin>
