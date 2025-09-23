<?php

namespace App\Traits;

use Illuminate\Support\Carbon;

trait Filterable
{
    protected function applyFilter($request, $defaultFilter = 'month')
    {
        $filter = $request->input('filter', $defaultFilter);
        $startInput = $request->input('start_date');
        $endInput = $request->input('end_date');

        // Validation với thông báo lỗi tiếng Việt, tùy theo filter
        $rules = [];
        $messages = [];

        if ($filter === 'day') {
            $rules = [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:' . now()->format('Y-m-d'),
            ];
            $messages = [
                'start_date.date' => 'Ngày bắt đầu phải là một ngày hợp lệ.',
                'end_date.date' => 'Ngày kết thúc phải là một ngày hợp lệ.',
                'end_date.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
                'end_date.before_or_equal' => 'Ngày kết thúc không được sau ngày hiện tại (' . now()->format('d/m/Y') . ').',
            ];
        } elseif ($filter === 'month') {
            $rules = [
                'start_date' => 'nullable|date_format:Y-m',
                'end_date' => 'nullable|date_format:Y-m|after_or_equal:start_date',
            ];
            $messages = [
                'start_date.date_format' => 'Tháng bắt đầu phải có định dạng YYYY-MM.',
                'end_date.date_format' => 'Tháng kết thúc phải có định dạng YYYY-MM.',
                'end_date.after_or_equal' => 'Tháng kết thúc phải sau hoặc bằng tháng bắt đầu.',
            ];
        } elseif ($filter === 'year') {
            $rules = [
                'start_date' => 'nullable|integer|min:1900|max:' . now()->year,
                'end_date' => 'nullable|integer|after_or_equal:start_date|max:' . now()->year,
            ];
            $messages = [
                'start_date.integer' => 'Năm bắt đầu phải là số nguyên.',
                'start_date.min' => 'Năm bắt đầu phải lớn hơn hoặc bằng 1900.',
                'start_date.max' => 'Năm bắt đầu không được sau năm hiện tại.',
                'end_date.integer' => 'Năm kết thúc phải là số nguyên.',
                'end_date.after_or_equal' => 'Năm kết thúc phải sau hoặc bằng năm bắt đầu.',
                'end_date.max' => 'Năm kết thúc không được sau năm hiện tại.',
            ];
        }

        $request->validate($rules, $messages);

        // Parse start_date và end_date dựa trên filter
        $startDate = null;
        $endDate = null;

        if ($filter === 'day') {
            $startDate = $startInput ? Carbon::parse($startInput)->startOfDay() : now()->subDays(29)->startOfDay();
            $endDate = $endInput ? Carbon::parse($endInput)->endOfDay() : now()->endOfDay();
        } elseif ($filter === 'month') {
            $startDate = $startInput ? Carbon::createFromFormat('Y-m', $startInput)->startOfMonth() : now()->startOfYear()->startOfMonth();
            $endDate = $endInput ? Carbon::createFromFormat('Y-m', $endInput)->endOfMonth() : now()->endOfMonth();
        } elseif ($filter === 'year') {
            $startDate = $startInput ? Carbon::create((int)$startInput, 1, 1)->startOfYear() : now()->subYears(9)->startOfYear();
            $endDate = $endInput ? Carbon::create((int)$endInput, 12, 31)->endOfYear() : now()->endOfYear();
        }

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
