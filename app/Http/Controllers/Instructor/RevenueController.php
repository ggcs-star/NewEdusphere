<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RevenueController extends Controller
{
    public function index(Request $request): View
    {
        $userId = current_admin_user()->id;

        $from = $request->get('from') ? Carbon::parse($request->get('from'))->startOfDay() : now()->subDays(29)->startOfDay();
        $to = $request->get('to') ? Carbon::parse($request->get('to'))->endOfDay() : now()->endOfDay();

        $query = Payment::with(['user', 'course'])
            ->whereHas('course', fn ($q) => $q->where('user_id', $userId))
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
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        if ($status = $request->get('status')) {
            if ($status === 'paid') {
                $query->where('instructor_paid_out', true);
            } elseif ($status === 'pending') {
                $query->where('instructor_paid_out', false);
            }
        }

        $totals = (clone $query)->toBase()
            ->selectRaw('COALESCE(SUM(amount),0) as total_amount, COALESCE(SUM(instructor_revenue),0) as total_instructor, COUNT(*) as total_sales')
            ->first();

        $paidOut = (clone $query)->where('instructor_paid_out', true)->sum('instructor_revenue');

        return view('admin.revenue.index', [
            'tab' => 'instructor',
            'isAdmin' => false,
            'routePrefix' => 'instructor',
            'indexUrl' => route('instructor.revenue.index'),
            'payments' => $query->paginate(15)->withQueryString(),
            'totals' => $totals,
            'paidOut' => $paidOut,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'courses' => Course::where('user_id', $userId)->orderBy('title')->get(['id', 'title']),
            'selectedCourse' => $request->get('course_id', 'all'),
            'selectedMethod' => $request->get('payment_method', 'all'),
            'selectedStatus' => $request->get('status', 'all'),
            'search' => $request->get('search', ''),
        ]);
    }
}
