<?php

namespace App\Services;

use Closure;
use Illuminate\Database\Eloquent\Builder;

/**
 * Total count for a filtered query, plus a genuine month-over-month trend
 * (how many matching rows were created this calendar month vs last month).
 * Used by the Courses stat cards; any other admin list can reuse it the
 * same way instead of hand-rolling the same date math again.
 */
class TrendStatsService
{
    /**
     * @param  class-string  $modelClass
     * @param  Closure(Builder): Builder  $scope
     */
    public function forModel(string $modelClass, Closure $scope): array
    {
        $count = $scope($modelClass::query())->count();

        $thisMonth = $scope($modelClass::query())
            ->whereBetween('created_at', [now()->startOfMonth(), now()])
            ->count();

        $lastMonth = $scope($modelClass::query())
            ->whereBetween('created_at', [
                now()->subMonthNoOverflow()->startOfMonth(),
                now()->subMonthNoOverflow()->endOfMonth(),
            ])
            ->count();

        if ($lastMonth > 0) {
            $change = (int) round((($thisMonth - $lastMonth) / $lastMonth) * 100);
        } elseif ($thisMonth > 0) {
            $change = 100;
        } else {
            $change = 0;
        }

        return [
            'count' => $count,
            'change' => $change,
            'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat'),
        ];
    }
}
