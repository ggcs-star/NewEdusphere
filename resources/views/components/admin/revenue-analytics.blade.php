@props(['analytics', 'periodRoute', 'revenueRoute', 'coursesRoute', 'instructorsRoute' => null, 'isAdmin' => false])

@php
    $periods = ['day' => 'Daily', 'week' => 'Weekly', 'month' => 'Monthly'];

    $farPast = '2000-01-01';
    $today = now()->format('Y-m-d');
    $revenueRanges = [
        'today' => ['from' => $today, 'to' => $today],
        'week' => ['from' => now()->startOfWeek()->format('Y-m-d'), 'to' => now()->endOfWeek()->format('Y-m-d')],
        'month' => ['from' => now()->startOfMonth()->format('Y-m-d'), 'to' => now()->endOfMonth()->format('Y-m-d')],
        'allTime' => ['from' => $farPast, 'to' => $today],
    ];

    $periodLabels = ['day' => 'Today', 'week' => 'This Week', 'month' => 'This Month'];
    $currencySymbol = config('currencies')[\App\Models\Setting::get('system_currency', 'USD')]['symbol'] ?? '$';
@endphp

{{-- Revenue trend chart --}}
<div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6 mb-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <div>
            <h3 class="font-semibold text-brand-950">Revenue Trend</h3>
            <p class="text-xs text-slate-400 mt-0.5">
                {{ $analytics['students']['total'] }} student{{ $analytics['students']['total'] === 1 ? '' : 's' }} total
                &middot; +{{ $analytics['students']['newThisMonth'] }} new this month
            </p>
        </div>
        <div class="inline-flex rounded-full bg-slate-100 p-1">
            @foreach ($periods as $value => $label)
                <a href="{{ route($periodRoute, ['period' => $value]) }}"
                    class="rounded-full px-4 py-1.5 text-xs font-semibold transition {{ $analytics['period'] === $value ? 'bg-brand-900 text-white' : 'text-slate-600 hover:bg-white' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    @if (! $analytics['trend']['hasData'])
        <x-admin.empty-state icon="fa-chart-line" title="No revenue in this range" description="Sales will plot here as soon as a payment comes in." />
    @endif

    <div x-show="{{ $analytics['trend']['hasData'] ? 'true' : 'false' }}" x-data="{
            init() {
                if (!{{ $analytics['trend']['hasData'] ? 'true' : 'false' }}) return;
                new Chart(this.$refs.revenueChart, {
                    type: 'line',
                    data: {
                        labels: @js($analytics['trend']['labels']),
                        datasets: [{
                            label: 'Revenue',
                            data: @js($analytics['trend']['data']),
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.08)',
                            borderWidth: 2,
                            tension: 0.35,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 4,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => @js($currencySymbol) + ctx.parsed.y.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                },
                            },
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: (value) => @js($currencySymbol) + value.toLocaleString() },
                                grid: { color: '#f1f5f9' },
                            },
                            x: { grid: { display: false } },
                        },
                    },
                });
            },
        }"
        class="h-72">
        <canvas x-ref="revenueChart"></canvas>
    </div>
</div>

{{-- Revenue stat cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
    <x-admin.stat-card
        icon="fa-calendar-day"
        color="sky"
        :count="format_price($analytics['revenue']['today']['amount'])"
        label="Today's Revenue"
        :change="$analytics['revenue']['today']['change']"
        :direction="$analytics['revenue']['today']['direction']"
        change-label="vs yesterday"
        :href="route($revenueRoute, $revenueRanges['today'])"
    />
    <x-admin.stat-card
        icon="fa-calendar-week"
        color="violet"
        :count="format_price($analytics['revenue']['week']['amount'])"
        label="This Week's Revenue"
        :change="$analytics['revenue']['week']['change']"
        :direction="$analytics['revenue']['week']['direction']"
        change-label="vs last week"
        :href="route($revenueRoute, $revenueRanges['week'])"
    />
    <x-admin.stat-card
        icon="fa-calendar"
        color="amber"
        :count="format_price($analytics['revenue']['month']['amount'])"
        label="This Month's Revenue"
        :change="$analytics['revenue']['month']['change']"
        :direction="$analytics['revenue']['month']['direction']"
        change-label="vs last month"
        :href="route($revenueRoute, $revenueRanges['month'])"
    />
    <x-admin.stat-card
        icon="fa-sack-dollar"
        color="emerald"
        :count="format_price($analytics['revenue']['allTime']['amount'])"
        label="All-Time Revenue"
        :change="0"
        direction="flat"
        :href="route($revenueRoute, $revenueRanges['allTime'])"
    />
</div>

{{-- Top courses / top instructors --}}
<div class="grid grid-cols-1 {{ $isAdmin ? 'lg:grid-cols-2' : '' }} gap-6 mb-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-brand-950">Top Courses by Revenue</h3>
            <a href="{{ route($coursesRoute) }}" class="text-sm font-semibold text-brand-700 hover:underline">View all</a>
        </div>
        @if (empty($analytics['topCourses']))
            <x-admin.empty-state icon="fa-book-open" title="No sales yet" description="Revenue-generating courses will show up here." />
        @else
            <div class="divide-y divide-slate-100">
                @foreach ($analytics['topCourses'] as $course)
                    <div class="flex items-center gap-3 py-3">
                        <div class="h-10 w-10 flex-shrink-0 rounded-lg bg-brand-500 flex items-center justify-center overflow-hidden">
                            @if ($course['thumbnail'])
                                <img src="{{ Storage::url($course['thumbnail']) }}" class="h-full w-full object-cover">
                            @else
                                <i class="fa-solid fa-book-open text-white text-sm"></i>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-brand-950 truncate" title="{{ $course['title'] }}">{{ $course['title'] }}</p>
                            <p class="text-xs text-slate-400">{{ $course['students'] }} student{{ $course['students'] === 1 ? '' : 's' }}</p>
                        </div>
                        <p class="text-sm font-bold text-emerald-600 flex-shrink-0">{{ format_price($course['revenue']) }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if ($isAdmin)
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-brand-950">Top Instructors by Revenue</h3>
                <a href="{{ route($instructorsRoute, ['role' => 'instructor']) }}" class="text-sm font-semibold text-brand-700 hover:underline">View all</a>
            </div>
            @if (empty($analytics['topInstructors']))
                <x-admin.empty-state icon="fa-chalkboard-user" title="No sales yet" description="Top-earning instructors will show up here." />
            @else
                <div class="divide-y divide-slate-100">
                    @foreach ($analytics['topInstructors'] as $instructor)
                        <div class="flex items-center gap-3 py-3">
                            <div class="h-9 w-9 rounded-full bg-brand-900 text-white flex items-center justify-center font-semibold text-xs flex-shrink-0">
                                {{ strtoupper(substr($instructor['name'], 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-brand-950 truncate">{{ $instructor['name'] }}</p>
                                <p class="text-xs text-slate-400">{{ $instructor['courses'] }} course{{ $instructor['courses'] === 1 ? '' : 's' }} &middot; {{ $instructor['sales'] }} sale{{ $instructor['sales'] === 1 ? '' : 's' }}</p>
                            </div>
                            <p class="text-sm font-bold text-emerald-600 flex-shrink-0">{{ format_price($instructor['revenue']) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>

{{-- Recent payments --}}
<div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-3 px-6 py-4 border-b border-slate-100">
        <div>
            <h3 class="font-semibold text-brand-950">Recent Payments</h3>
            <p class="text-xs text-slate-400 mt-0.5">Showing: {{ $periodLabels[$analytics['period']] }}</p>
        </div>
        <div class="inline-flex rounded-full bg-slate-100 p-1">
            @foreach ($periods as $value => $label)
                <a href="{{ route($periodRoute, ['period' => $value]) }}"
                    class="rounded-full px-4 py-1.5 text-xs font-semibold transition {{ $analytics['period'] === $value ? 'bg-brand-900 text-white' : 'text-slate-600 hover:bg-white' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
    @if (empty($analytics['recentPayments']))
        <div class="p-6">
            <x-admin.empty-state icon="fa-receipt" title="No payments {{ strtolower($periodLabels[$analytics['period']]) }}" description="Try a wider range, or new sales will appear here as they come in." />
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-50/60 text-left text-sm font-semibold uppercase tracking-wide text-brand-700">
                        <th class="px-6 py-3">Student</th>
                        <th class="px-6 py-3">Course</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($analytics['recentPayments'] as $payment)
                        <tr class="hover:bg-brand-50/30 transition">
                            <td class="px-6 py-3.5 font-semibold text-brand-950">{{ $payment['student'] }}</td>
                            <td class="px-6 py-3.5 text-slate-600 truncate max-w-xs" title="{{ $payment['course'] }}">{{ $payment['course'] }}</td>
                            <td class="px-6 py-3.5 font-semibold text-emerald-600">{{ format_price($payment['amount']) }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $payment['date']->format('D, d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
