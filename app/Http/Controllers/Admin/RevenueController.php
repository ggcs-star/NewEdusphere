<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RevenueController extends BaseAdminController
{
    private const TABS = ['admin', 'instructor'];

    public function index(Request $request, string $tab = 'admin'): View
    {
        if (! in_array($tab, self::TABS)) {
            abort(404);
        }

        $from = $request->get('from') ? Carbon::parse($request->get('from'))->startOfDay() : now()->subDays(29)->startOfDay();
        $to = $request->get('to') ? Carbon::parse($request->get('to'))->endOfDay() : now()->endOfDay();

        $query = Payment::with(['user', 'course.instructor'])
            ->whereBetween('created_at', [$from, $to])
            ->latest();

        if ($courseId = $request->get('course_id')) {
            if ($courseId !== 'all') {
                $query->where('course_id', $courseId);
            }
        }

        if ($method = $request->get('payment_method')) {
            if ($method !== 'all') {
                $query->where('payment_method', $method);
            }
        }

        if ($search = $request->get('search')) {
            if ($tab === 'admin') {
                $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%"));
            } else {
                $query->whereHas('course.instructor', fn ($q) => $q->where('name', 'like', "%{$search}%"));
            }
        }

        if ($tab === 'instructor' && ($status = $request->get('status'))) {
            if ($status === 'paid') {
                $query->where('instructor_paid_out', true);
            } elseif ($status === 'pending') {
                $query->where('instructor_paid_out', false);
            }
        }

        $totals = (clone $query)->toBase()
            ->selectRaw('COALESCE(SUM(amount),0) as total_amount, COALESCE(SUM(admin_revenue),0) as total_admin, COALESCE(SUM(instructor_revenue),0) as total_instructor, COUNT(*) as total_sales')
            ->first();

        $paidOut = (clone $query)->where('instructor_paid_out', true)->sum('instructor_revenue');

        return view('admin.revenue.index', [
            'tab' => $tab,
            'payments' => $query->paginate(15)->withQueryString(),
            'totals' => $totals,
            'paidOut' => $paidOut,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'courses' => Course::orderBy('title')->get(['id', 'title']),
            'selectedCourse' => $request->get('course_id', 'all'),
            'selectedMethod' => $request->get('payment_method', 'all'),
            'selectedStatus' => $request->get('status', 'all'),
            'search' => $request->get('search', ''),
        ]);
    }

    public function markPaid(Payment $payment): RedirectResponse
    {
        return $this->tryActionBack(function () use ($payment) {
            $payment->update(['instructor_paid_out' => true]);
        }, 'Instructor payout marked as paid.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        return $this->tryActionBack(function () use ($payment) {
            $payment->delete();
        }, 'Payment record removed.');
    }
}
