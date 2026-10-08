<x-layouts.admin title="Revenue">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-brand-950">Revenue</h2>
        <p class="text-sm text-slate-500 mt-1">Track platform earnings and instructor payouts.</p>
    </div>

    <form method="GET" class="flex flex-wrap items-end gap-3 mb-6">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}" class="rounded-lg bg-white border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-200 outline-none">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}" class="rounded-lg bg-white border border-slate-200 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-200 outline-none">
        </div>
        <button type="submit" class="rounded-full bg-brand-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-950 transition">Apply</button>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-6">
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-sm text-slate-500">Total Sales</p>
            <p class="mt-2 text-3xl font-bold text-brand-950">${{ number_format($totals->total_amount, 2) }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $totals->total_sales }} transactions</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-sm text-slate-500">Platform Revenue</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600">${{ number_format($totals->total_admin, 2) }}</p>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-sm text-slate-500">Instructor Payouts</p>
            <p class="mt-2 text-3xl font-bold text-accent-600">${{ number_format($totals->total_instructor, 2) }}</p>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-50/60 text-left text-sm font-semibold uppercase tracking-wide text-brand-700">
                        <th class="px-5 py-3">Student</th>
                        <th class="px-5 py-3">Course</th>
                        <th class="px-5 py-3">Method</th>
                        <th class="px-5 py-3">Amount</th>
                        <th class="px-5 py-3">Platform</th>
                        <th class="px-5 py-3">Instructor</th>
                        <th class="px-5 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-brand-50/30 transition">
                            <td class="px-5 py-3.5 text-lg font-semibold text-brand-950">{{ $payment->user->name ?? '—' }}</td>
                            <td class="px-5 py-3.5 text-slate-600 truncate max-w-xs" title="{{ $payment->course->title ?? '' }}">{{ $payment->course->title ?? '—' }}</td>
                            <td class="px-5 py-3.5"><x-admin.badge color="brand">{{ ucfirst($payment->payment_method) }}</x-admin.badge></td>
                            <td class="px-5 py-3.5 font-semibold text-brand-950">${{ number_format($payment->amount, 2) }}</td>
                            <td class="px-5 py-3.5 text-emerald-600">${{ number_format($payment->admin_revenue, 2) }}</td>
                            <td class="px-5 py-3.5 text-accent-600">${{ number_format($payment->instructor_revenue, 2) }}</td>
                            <td class="px-5 py-3.5 text-slate-500">{{ $payment->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-16 text-center text-slate-400">
                                <i class="fa-solid fa-sack-dollar text-2xl mb-2 block"></i>
                                No payments in this date range.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-admin.pagination :paginator="$payments" />
</x-layouts.admin>
