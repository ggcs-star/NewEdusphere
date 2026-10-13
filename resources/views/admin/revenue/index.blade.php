<x-layouts.admin title="Revenue">
    {{-- Welcome banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-brand-100 px-6 sm:px-8 py-5 mb-8">
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
            <div class="max-w-lg">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-950 tracking-tight">Revenue</h2>
                <p class="mt-1 text-sm font-semibold text-accent-600">
                    {{ $tab === 'admin' ? 'Platform earnings from every course sale.' : 'Instructor payouts and their payment status.' }}
                </p>
            </div>

            <div class="hidden lg:block relative w-20 h-20 flex-shrink-0">
                <div class="h-20 w-20 rounded-3xl bg-brand-500 rotate-6 shadow-xl flex items-center justify-center">
                    <i class="fa-solid fa-sack-dollar text-3xl text-accent-400 -rotate-6"></i>
                </div>
                <div class="absolute -bottom-3 -left-4 h-10 w-14 rounded-xl bg-accent-500 shadow-lg flex items-center justify-center -rotate-12">
                    <i class="fa-solid fa-chart-line text-base text-white"></i>
                </div>
            </div>
        </div>

        <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-brand-200/50 blur-2xl"></div>
        <div class="pointer-events-none absolute right-24 bottom-0 h-28 w-28 rounded-full bg-accent-400/20 blur-2xl"></div>
    </div>

    {{-- Tab nav --}}
    <div class="inline-flex rounded-full bg-white border border-slate-200 shadow-sm p-1 mb-6">
        <a href="{{ route('admin.revenue.index', ['tab' => 'admin', 'from' => $from, 'to' => $to]) }}"
            class="rounded-full px-5 py-2 text-sm font-semibold transition {{ $tab === 'admin' ? 'bg-brand-900 text-white' : 'text-slate-600 hover:bg-brand-50' }}">
            <i class="fa-solid fa-building-columns text-xs mr-1.5"></i> Admin Revenue
        </a>
        <a href="{{ route('admin.revenue.index', ['tab' => 'instructor', 'from' => $from, 'to' => $to]) }}"
            class="rounded-full px-5 py-2 text-sm font-semibold transition {{ $tab === 'instructor' ? 'bg-brand-900 text-white' : 'text-slate-600 hover:bg-brand-50' }}">
            <i class="fa-solid fa-chalkboard-user text-xs mr-1.5"></i> Instructor Revenue
        </a>
    </div>

    <form method="GET" action="{{ route('admin.revenue.index', $tab) }}" class="grid grid-flow-col auto-cols-max items-end gap-3 mb-6">
        <div class="min-w-56">
            <label class="block text-xs font-medium text-slate-500 mb-1">{{ $tab === 'admin' ? 'Student' : 'Instructor' }}</label>
            <x-admin.search-input :value="$search" :placeholder="$tab === 'admin' ? 'Search student...' : 'Search instructor...'" class="!w-full !max-w-none" />
        </div>

        <div class="min-w-44">
            <label class="block text-xs font-medium text-slate-500 mb-1">Course</label>
            <x-admin.searchable-select
                name="course_id"
                :options="$courses->pluck('title', 'id')"
                :selected="$selectedCourse"
                all-label="All Courses"
                pill
                class="w-full"
            />
        </div>

        <div class="min-w-40">
            <label class="block text-xs font-medium text-slate-500 mb-1">Payment Method</label>
            <x-admin.searchable-select
                name="payment_method"
                :options="['paypal' => 'PayPal', 'stripe' => 'Stripe', 'free' => 'Free']"
                :selected="$selectedMethod"
                all-label="All Methods"
                pill
                class="w-full"
            />
        </div>

        @if ($tab === 'instructor')
            <div class="min-w-36">
                <label class="block text-xs font-medium text-slate-500 mb-1">Status</label>
                <x-admin.searchable-select
                    name="status"
                    :options="['paid' => 'Paid', 'pending' => 'Pending']"
                    :selected="$selectedStatus"
                    all-label="All Statuses"
                    pill
                    class="w-full"
                />
            </div>
        @endif

        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}" class="rounded-lg bg-white border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-200 outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}" class="rounded-lg bg-white border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-200 outline-none">
        </div>

        <button type="submit" class="rounded-full bg-brand-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-950 transition">Apply</button>
        @if ($search || $selectedCourse !== 'all' || $selectedMethod !== 'all' || $selectedStatus !== 'all')
            <a href="{{ route('admin.revenue.index', $tab) }}" class="text-sm font-semibold text-slate-500 hover:text-slate-700 pb-2.5">Clear</a>
        @endif
    </form>

    @if ($tab === 'admin')
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
            <x-admin.stat-card
                icon="fa-receipt"
                color="violet"
                :count="'$'.number_format($totals->total_amount, 2)"
                :label="$totals->total_sales.' transactions'"
            />
            <x-admin.stat-card
                icon="fa-building-columns"
                color="emerald"
                :count="'$'.number_format($totals->total_admin, 2)"
                label="Platform Revenue"
            />
            <x-admin.stat-card
                icon="fa-hand-holding-dollar"
                color="amber"
                :count="'$'.number_format($totals->total_instructor, 2)"
                label="Instructor Payouts"
            />
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-brand-50/60 text-left text-sm font-semibold uppercase tracking-wide text-brand-700">
                            <th class="px-5 py-3">Student</th>
                            <th class="px-5 py-3">Enrolled Course</th>
                            <th class="px-5 py-3">Total Amount</th>
                            <th class="px-5 py-3">Admin Revenue</th>
                            <th class="px-5 py-3">Enrolment Date</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-brand-50/30 transition">
                                <td class="px-5 py-3.5 text-sm font-semibold text-brand-950">{{ $payment->user->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-slate-600 truncate max-w-xs" title="{{ $payment->course->title ?? '' }}">{{ $payment->course->title ?? '—' }}</td>
                                <td class="px-5 py-3.5 font-semibold text-brand-950">${{ number_format($payment->amount, 2) }}</td>
                                <td class="px-5 py-3.5 text-emerald-600 font-semibold">${{ number_format($payment->admin_revenue, 2) }}</td>
                                <td class="px-5 py-3.5 text-slate-500">{{ $payment->created_at->format('D, d M Y') }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end">
                                        <form action="{{ route('admin.revenue.destroy', $payment) }}" method="POST" onsubmit="return confirm('Remove this payment record?');">
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
                                <td colspan="6" class="px-5 py-16 text-center text-slate-400">
                                    <i class="fa-solid fa-sack-dollar text-2xl mb-2 block"></i>
                                    No payments in this date range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
            <x-admin.stat-card
                icon="fa-chalkboard-user"
                color="violet"
                :count="'$'.number_format($totals->total_instructor, 2)"
                :label="$totals->total_sales.' transactions'"
            />
            <x-admin.stat-card
                icon="fa-circle-check"
                color="emerald"
                :count="'$'.number_format($paidOut, 2)"
                label="Paid Out"
            />
            <x-admin.stat-card
                icon="fa-clock"
                color="amber"
                :count="'$'.number_format($totals->total_instructor - $paidOut, 2)"
                label="Pending Payout"
            />
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-brand-50/60 text-left text-sm font-semibold uppercase tracking-wide text-brand-700">
                            <th class="px-5 py-3">Enrolled Course</th>
                            <th class="px-5 py-3">Instructor</th>
                            <th class="px-5 py-3">Instructor Revenue</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-brand-50/30 transition">
                                <td class="px-5 py-3.5">
                                    <p class="text-sm font-semibold text-brand-950 truncate max-w-xs" title="{{ $payment->course->title ?? '' }}">{{ $payment->course->title ?? '—' }}</p>
                                    <p class="text-xs text-slate-400">Enrolled {{ $payment->created_at->format('D, d M Y') }}</p>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">{{ $payment->course->instructor->name ?? '—' }}</td>
                                <td class="px-5 py-3.5">
                                    <p class="font-semibold text-accent-600">${{ number_format($payment->instructor_revenue, 2) }}</p>
                                    <p class="text-xs text-slate-400">Total: ${{ number_format($payment->amount, 2) }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <x-admin.badge :color="$payment->instructor_paid_out ? 'emerald' : 'slate'">{{ $payment->instructor_paid_out ? 'Paid' : 'Pending' }}</x-admin.badge>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end">
                                        @if ($payment->instructor_paid_out)
                                            <span class="text-xs text-slate-400">—</span>
                                        @else
                                            <form action="{{ route('admin.revenue.mark-paid', $payment) }}" method="POST" onsubmit="return confirm('Mark this payout as paid?');">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="rounded-full bg-brand-900 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-brand-950 transition">
                                                    Mark as Paid
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center text-slate-400">
                                    <i class="fa-solid fa-sack-dollar text-2xl mb-2 block"></i>
                                    No payments in this date range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <x-admin.pagination :paginator="$payments" />
</x-layouts.admin>
