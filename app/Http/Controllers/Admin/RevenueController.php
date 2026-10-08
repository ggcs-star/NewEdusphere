<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RevenueController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->get('from') ? Carbon::parse($request->get('from'))->startOfDay() : now()->subDays(29)->startOfDay();
        $to = $request->get('to') ? Carbon::parse($request->get('to'))->endOfDay() : now()->endOfDay();

        $payments = Payment::with(['user', 'course'])
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totals = Payment::whereBetween('created_at', [$from, $to])
            ->selectRaw('COALESCE(SUM(amount),0) as total_amount, COALESCE(SUM(admin_revenue),0) as total_admin, COALESCE(SUM(instructor_revenue),0) as total_instructor, COUNT(*) as total_sales')
            ->first();

        return view('admin.revenue.index', [
            'payments' => $payments,
            'totals' => $totals,
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
        ]);
    }
}
