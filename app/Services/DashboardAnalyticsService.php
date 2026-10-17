<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Builds every number the Admin and Instructor dashboards show: revenue stat
 * cards, a revenue trend chart bucketed by day/week/month, and course/student
 * breakdowns. Pass $instructorId to scope everything to one instructor's own
 * courses (Instructor dashboard); leave it null for the platform-wide view
 * (Admin dashboard).
 *
 * Counts every Payment row as revenue regardless of its `status` column —
 * matching Admin\RevenueController, which is the established ledger admins
 * already rely on and never filters by status either. (`status` tracks
 * whether the Razorpay charge itself was confirmed; `instructor_paid_out`
 * is the separate "has the instructor been paid their cut yet" flag shown
 * as Pending/Paid on the Revenue page — neither gates whether a sale counts
 * here.)
 */
class DashboardAnalyticsService
{
    private const PERIODS = ['day', 'week', 'month'];

    public function build(?int $instructorId, string $period): array
    {
        $period = in_array($period, self::PERIODS, true) ? $period : 'week';

        return [
            'period' => $period,
            'revenue' => $this->revenueStats($instructorId),
            'trend' => $this->revenueTrend($instructorId, $period),
            'topCourses' => $this->topCourses($instructorId),
            'topInstructors' => $instructorId ? [] : $this->topInstructors(),
            'recentPayments' => $this->recentPayments($instructorId, $period),
            'students' => $this->studentStats($instructorId),
        ];
    }

    private function paymentQuery(?int $instructorId): Builder
    {
        $query = Payment::query();

        if ($instructorId) {
            $query->whereHas('course', fn (Builder $q) => $q->where('user_id', $instructorId));
        }

        return $query;
    }

    private function sumAmount(Builder $query, Carbon $from, Carbon $to): float
    {
        return (float) (clone $query)->whereBetween('created_at', [$from, $to])->sum('amount');
    }

    private function percentChange(float $current, float $previous): array
    {
        if ($previous > 0) {
            $change = (int) round((($current - $previous) / $previous) * 100);
        } elseif ($current > 0) {
            $change = 100;
        } else {
            $change = 0;
        }

        return [
            'change' => $change,
            'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat'),
        ];
    }

    private function revenueStats(?int $instructorId): array
    {
        $query = $this->paymentQuery($instructorId);

        $today = $this->sumAmount($query, now()->startOfDay(), now()->endOfDay());
        $yesterday = $this->sumAmount($query, now()->subDay()->startOfDay(), now()->subDay()->endOfDay());

        $thisWeek = $this->sumAmount($query, now()->startOfWeek(), now()->endOfWeek());
        $lastWeek = $this->sumAmount($query, now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek());

        $thisMonth = $this->sumAmount($query, now()->startOfMonth(), now()->endOfMonth());
        $lastMonth = $this->sumAmount($query, now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth());

        $allTime = (float) (clone $query)->sum('amount');

        return [
            'today' => ['amount' => $today, ...$this->percentChange($today, $yesterday)],
            'week' => ['amount' => $thisWeek, ...$this->percentChange($thisWeek, $lastWeek)],
            'month' => ['amount' => $thisMonth, ...$this->percentChange($thisMonth, $lastMonth)],
            'allTime' => ['amount' => $allTime],
        ];
    }

    private function revenueTrend(?int $instructorId, string $period): array
    {
        [$bucketExpr, $from, $labelFormatter] = match ($period) {
            'day' => ['DATE(created_at)', now()->subDays(13)->startOfDay(), fn (string $key) => Carbon::parse($key)->format('d M')],
            'week' => ["YEARWEEK(created_at, 3)", now()->subWeeks(11)->startOfWeek(), fn (string $key) => 'Wk '.substr($key, -2).' \''.substr($key, 2, 2)],
            'month' => ["DATE_FORMAT(created_at, '%Y-%m')", now()->subMonthsNoOverflow(11)->startOfMonth(), fn (string $key) => Carbon::parse($key.'-01')->format('M Y')],
        };

        $rows = $this->paymentQuery($instructorId)
            ->where('created_at', '>=', $from)
            ->selectRaw("{$bucketExpr} as bucket, COALESCE(SUM(amount),0) as total")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()
            ->pluck('total', 'bucket');

        // Walk every bucket in range (not just ones with sales) so the chart
        // shows a continuous line instead of skipping zero-revenue periods.
        $labels = [];
        $data = [];
        $cursor = $from->copy();
        $end = now();

        while ($cursor->lte($end)) {
            $key = match ($period) {
                'day' => $cursor->format('Y-m-d'),
                'week' => $cursor->format('oW'),
                'month' => $cursor->format('Y-m'),
            };

            $labels[] = $labelFormatter($key);
            $data[] = round((float) ($rows[$key] ?? 0), 2);

            $cursor = match ($period) {
                'day' => $cursor->addDay(),
                'week' => $cursor->addWeek(),
                'month' => $cursor->addMonthNoOverflow(),
            };
        }

        return ['labels' => $labels, 'data' => $data, 'hasData' => array_sum($data) > 0];
    }

    private function topCourses(?int $instructorId): array
    {
        $query = Course::query()
            ->withCount(['enrollments'])
            ->withSum('payments as revenue', 'amount');

        if ($instructorId) {
            $query->where('user_id', $instructorId);
        }

        return $query->having('revenue', '>', 0)
            ->orderByDesc('revenue')
            ->take(5)
            ->get(['id', 'title', 'thumbnail', 'status'])
            ->map(fn (Course $course) => [
                'title' => $course->title,
                'thumbnail' => $course->thumbnail,
                'status' => $course->status,
                'revenue' => (float) ($course->revenue ?? 0),
                'students' => $course->enrollments_count,
            ])
            ->all();
    }

    private function topInstructors(): array
    {
        return DB::table('payments')
            ->join('courses', 'courses.id', '=', 'payments.course_id')
            ->join('users', 'users.id', '=', 'courses.user_id')
            ->selectRaw('users.id, users.name, COALESCE(SUM(payments.amount),0) as revenue, COUNT(DISTINCT payments.id) as sales, COUNT(DISTINCT courses.id) as course_count')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('revenue')
            ->take(5)
            ->get()
            ->map(fn ($row) => [
                'name' => $row->name,
                'revenue' => (float) $row->revenue,
                'sales' => (int) $row->sales,
                'courses' => (int) $row->course_count,
            ])
            ->all();
    }

    private function recentPayments(?int $instructorId, string $period): array
    {
        [$from, $to] = match ($period) {
            'day' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
        };

        return $this->paymentQuery($instructorId)
            ->whereBetween('created_at', [$from, $to])
            ->with(['user:id,name,email', 'course:id,title'])
            ->latest()
            ->take(8)
            ->get()
            ->map(fn (Payment $payment) => [
                'student' => $payment->user->name ?? '—',
                'course' => $payment->course->title ?? '—',
                'amount' => (float) $payment->amount,
                'date' => $payment->created_at,
            ])
            ->all();
    }

    private function studentStats(?int $instructorId): array
    {
        $query = Enrollment::query();

        if ($instructorId) {
            $query->whereHas('course', fn (Builder $q) => $q->where('user_id', $instructorId));
        }

        $total = (clone $query)->distinct()->count('user_id');

        // "New" means first-ever enrollment in the scope, not just any
        // enrollment event this month — a student re-enrolling in a second
        // course shouldn't count as a new student.
        $firstEnrollments = DB::query()->fromSub(
            (clone $query)->selectRaw('user_id, MIN(created_at) as first_enrolled_at')->groupBy('user_id'),
            'first_enrollments'
        );

        $thisMonth = (clone $firstEnrollments)->whereBetween('first_enrolled_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $lastMonth = (clone $firstEnrollments)->whereBetween('first_enrolled_at', [
            now()->subMonthNoOverflow()->startOfMonth(),
            now()->subMonthNoOverflow()->endOfMonth(),
        ])->count();

        return [
            'total' => $total,
            'newThisMonth' => $thisMonth,
            ...$this->percentChange($thisMonth, $lastMonth),
        ];
    }
}
