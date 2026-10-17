<x-layouts.admin title="Dashboard">
    {{-- Welcome banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-brand-100 px-6 sm:px-8 py-5 mb-8">
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
            <div class="max-w-lg">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-950 tracking-tight">Welcome back, {{ current_admin_user()->name ?? 'Admin' }}</h2>
                <p class="mt-1 text-sm font-semibold text-accent-600">Here's how the platform is doing today.</p>
            </div>

            <div class="hidden lg:block relative w-20 h-20 flex-shrink-0">
                <div class="h-20 w-20 rounded-3xl bg-brand-500 rotate-6 shadow-xl flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-3xl text-accent-400 -rotate-6"></i>
                </div>
                <div class="absolute -bottom-3 -left-4 h-10 w-14 rounded-xl bg-accent-500 shadow-lg flex items-center justify-center -rotate-12">
                    <i class="fa-solid fa-graduation-cap text-base text-white"></i>
                </div>
            </div>
        </div>

        <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-brand-200/50 blur-2xl"></div>
        <div class="pointer-events-none absolute right-24 bottom-0 h-28 w-28 rounded-full bg-accent-400/20 blur-2xl"></div>
    </div>

    @if ($pendingCourses > 0)
        <a href="{{ route('admin.courses.pending') }}"
            class="mb-8 flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800 hover:bg-amber-100 transition">
            <i class="fa-solid fa-hourglass-half text-amber-500"></i>
            <span><strong>{{ $pendingCourses }}</strong> course{{ $pendingCourses === 1 ? '' : 's' }} waiting for your review.</span>
            <span class="ml-auto font-semibold">Review now &rarr;</span>
        </a>
    @endif

    {{-- Catalog stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        <x-admin.stat-card
            icon="fa-layer-group"
            color="violet"
            :count="$stats['categories']['count']"
            label="Categories"
            :change="$stats['categories']['change']"
            :direction="$stats['categories']['direction']"
            :href="route('admin.categories.index')"
        />
        <x-admin.stat-card
            icon="fa-book-open"
            color="emerald"
            :count="$stats['activeCourses']['count']"
            label="Active Courses"
            :change="$stats['activeCourses']['change']"
            :direction="$stats['activeCourses']['direction']"
            :href="route('admin.courses.index', ['status' => 'active'])"
        />
        <x-admin.stat-card
            icon="fa-user-graduate"
            color="amber"
            :count="$stats['students']['count']"
            label="Students"
            :change="$stats['students']['change']"
            :direction="$stats['students']['direction']"
            :href="route('admin.users.index', ['role' => 'user'])"
        />
    </div>

    <x-admin.revenue-analytics
        :analytics="$analytics"
        period-route="admin.dashboard"
        revenue-route="admin.revenue.index"
        courses-route="admin.courses.index"
        instructors-route="admin.users.index"
        :is-admin="true"
    />
</x-layouts.admin>
