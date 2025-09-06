<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait Filterable
{
    protected function applyFilter($request, $defaultFilter = 'day')
    {
        $filter = $request->input('filter', $defaultFilter);
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Đặt end_date mặc định là cuối ngày hiện tại
        if (!$endDate) {
            $endDate = now()->endOfDay();
        } else {
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        // Đặt start_date mặc định
        if (!$startDate) {
            switch ($filter) {
                case 'day':
                    $startDate = now()->subDays(6)->startOfDay(); // 7 ngày trước ngày hiện tại
                    break;
                case 'month':
                    $startDate = $endDate->copy()->subMonths(11)->startOfMonth();
                    break;
                case 'year':
                    $startDate = $endDate->copy()->subYears(9)->startOfYear();
                    break;
                default:
                    $startDate = now()->subDays(6)->startOfDay(); // 7 ngày trước ngày hiện tại
                    break;
            }
        } else {
            $startDate = Carbon::parse($startDate)->startOfDay();
        }

        // Validation với thông báo lỗi tiếng Việt
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:' . now()->format('Y-m-d'),
        ], [
            'start_date.date' => 'Ngày bắt đầu phải là một ngày hợp lệ.',
            'end_date.date' => 'Ngày kết thúc phải là một ngày hợp lệ.',
            'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'end_date.before_or_equal' => 'Ngày kết thúc không được sau ngày hiện tại (' . now()->format('d/m/Y') . ').',
        ]);

        // Giới hạn phạm vi
        switch ($filter) {
            case 'day':
                $maxDays = 31;
                if ($startDate->diffInDays($endDate) > $maxDays) {
                    $endDate = $startDate->copy()->addDays($maxDays)->endOfDay();
                }
                $groupBy = 'DATE(created_at)';
                $interval = '1 day';
                break;
            case 'month':
                $maxMonths = 12;
                if ($startDate->diffInMonths($endDate) > $maxMonths) {
                    $endDate = $startDate->copy()->addMonths($maxMonths)->endOfMonth();
                }
                $groupBy = 'DATE_FORMAT(created_at, "%Y-%m")';
                $interval = '1 month';
                break;
            case 'year':
                $maxYears = 10;
                if ($startDate->diffInYears($endDate) > $maxYears) {
                    $endDate = $startDate->copy()->addYears($maxYears)->endOfYear();
                }
                $groupBy = 'YEAR(created_at)';
                $interval = '1 year';
                break;
            default:
                $groupBy = 'DATE(created_at)';
                $interval = '1 day';
                break;
        }

        return [
            'filter' => $filter,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'group_by' => $groupBy,
            'interval' => $interval,
        ];
    }

    protected function generateLabels($startDate, $endDate, $filter, $interval)
    {
        $labels = [];

        if ($filter == 'year') {
            $startYear = $startDate->year;
            $endYear = $endDate->year;
            for ($year = $startYear; $year <= $endYear; $year++) {
                $labels[] = ['label' => (string)$year, 'key' => (string)$year];
            }
        } elseif ($filter == 'month') {
            $current = $startDate->copy()->startOfMonth();
            $end = $endDate->copy()->startOfMonth();

            while ($current <= $end) {
                $formattedDate = $current->format('Y-m');
                $labels[] = ['label' => $formattedDate, 'key' => $formattedDate];
                $current->addMonth();
            }
        } else {
            $dateRange = \Carbon\CarbonPeriod::create($startDate, $interval, $endDate);

            foreach ($dateRange as $date) {
                $formattedDate = $date->format('Y-m-d');
                if (!in_array($formattedDate, array_column($labels, 'label'))) {
                    $labels[] = ['label' => $formattedDate, 'key' => $formattedDate];
                }
            }
        }

        return $labels;
    }
}
