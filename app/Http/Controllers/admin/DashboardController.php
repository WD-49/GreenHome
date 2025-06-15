<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // dd(Auth::user()->profile);
        $title = 'dashboard';
        return view('admin.dashboard', compact('title'));
    }

    public function data()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        // Dữ liệu 7 ngày gần nhất
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $dates->push($today->copy()->subDays($i)->format('Y-m-d'));
        }
        // dd($dates);


        // Đơn hàng hôm nay & hôm qua
        $ordersToday = Order::whereDate('created_at', $today)->count();
        $ordersYesterday = Order::whereDate('created_at', $yesterday)->count();

        $ordersPerDay = Order::whereBetween('created_at', [Carbon::today()->subDays(6), Carbon::today()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $ordersLast7Days = $dates->map(function ($date) use ($ordersPerDay) {
            return $ordersPerDay->get($date, 0);
        });

        // doanh thu hôm nay & hôm qua
        $salesToday = Order::whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        $salesYesterday = Order::whereDate('created_at', $yesterday)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Doanh thu 7 ngày gần nhất (chỉ tính đơn đang vận chuyển và đã thanh toán)
        $salesPerday = Order::whereBetween('created_at', [Carbon::today()->subDays(6), Carbon::today()->endOfDay()])
            ->where('payment_status', 'paid')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->map(function ($value) {
                return $value ?: 0; // Đảm bảo giá trị không null
            });

        $salesLast7Days = $dates->map(function ($date) use ($salesPerday) {
            return $salesPerday->get($date, 0);
        });
        // dd($salesToday, $salesYesterday, $salesPercentChange);

        // Khách hàng mới hôm nay & hôm qua
        $newCustomersToday = User::whereDate('created_at', $today)->count();
        $newCustomersYesterday = User::whereDate('created_at', $yesterday)->count();
        // dd($newCustomersToday, $newCustomersYesterday);

        $newCustomersPercent = $newCustomersYesterday == 0
            ? ($newCustomersToday > 0 ? 100 : 0)
            : round((($newCustomersToday - $newCustomersYesterday) / $newCustomersYesterday) * 100, 2);

        // 7 ngày gần nhất cho khách hàng mới
        $customersPerDay = User::whereBetween('created_at', [$today->copy()->subDays(6), $today->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');
        // dd($customersPerDay);
        $newCustomersLast7Days = $dates->map(fn($date) => (int)($customersPerDay[$date] ?? 0));
        $newCustomersLast7DaysLabels = $dates->map(fn($date) => Carbon::parse($date)->format('d/m'));

        // Tổng số sản phẩm đã bán hôm nay & hôm qua
        $totalProductsSoldToday = OrderItem::whereDate('created_at', $today)->sum('quantity');
        $totalProductsSoldYesterday = OrderItem::whereDate('created_at', $yesterday)->sum('quantity');
        $totalProductsSoldPercent = $totalProductsSoldYesterday == 0
            ? ($totalProductsSoldToday > 0 ? 100 : 0)
            : round((($totalProductsSoldToday - $totalProductsSoldYesterday) / $totalProductsSoldYesterday) * 100, 2);

        // 7 ngày gần nhất cho tổng số sản phẩm đã bán
        $productsSoldPerDay = OrderItem::whereBetween('created_at', [$today->copy()->subDays(6), $today->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
            ->groupBy('date')
            ->pluck('total', 'date');
        $totalProductsSoldLast7Days = $dates->map(fn($date) => (int)($productsSoldPerDay[$date] ?? 0));
        $totalProductsSoldLast7DaysLabels = $dates->map(fn($date) => Carbon::parse($date)->format('d/m'));
        // dd($totalProductsSoldToday, $totalProductsSoldYesterday, $totalProductsSoldPercent, $totalProductsSoldLast7Days, $totalProductsSoldLast7DaysLabels);

        // Trả về dữ liệu dưới dạng JSON
        return response()->json([
            'orders_today' => (int)$ordersToday,
            'orders_yesterday' => (int)$ordersYesterday,
            'orders_percent_change' => $ordersYesterday == 0 ? ($ordersToday > 0 ? 100 : 0) : round((($ordersToday - $ordersYesterday) / $ordersYesterday) * 100, 2),
            'orders_last_7_days' => $ordersLast7Days->map(fn($v) => (int)$v),
            'orders_last_7_days_labels' => $dates->map(fn($date) => Carbon::parse($date)->format('d/m')),

            'sales_today' => (float)$salesToday,
            'sales_yesterday' => (float)$salesYesterday,
            'sales_percent_change' => $salesYesterday == 0 ? ($salesToday > 0 ? 100 : 0) : round((($salesToday - $salesYesterday) / $salesYesterday) * 100, 2),
            'sales_last_7_days' => $salesLast7Days->map(fn($v) => (float)$v),
            'sales_last_7_days_labels' => $dates->map(fn($date) => Carbon::parse($date)->format('d/m')),

            'new_customers_today' => $newCustomersToday,
            'new_customers_yesterday' => $newCustomersYesterday,
            'new_customers_percent' => $newCustomersPercent,
            'new_customers_last_7_days' => $newCustomersLast7Days,
            'new_customers_last_7_days_labels' => $newCustomersLast7DaysLabels,

            'total_products_sold_today' => (int)$totalProductsSoldToday,
            'total_products_sold_yesterday' => (int)$totalProductsSoldYesterday,
            'total_products_sold_percent' => $totalProductsSoldPercent,
            'total_products_sold_last_7_days' => $totalProductsSoldLast7Days,
            'total_products_sold_last_7_days_labels' => $totalProductsSoldLast7DaysLabels,




        ]);
    }

    public function repeatCustomerRate()
    {
        $type = request('range', 'day'); // day, week, month

        if ($type === 'week') {
            $labels = collect();
            $weeks = [];
            for ($i = 9; $i >= 0; $i--) {
                $start = Carbon::now()->startOfWeek()->subWeeks($i);
                $end = $start->copy()->endOfWeek();
                $weeks[] = [$start->copy(), $end->copy()];
                $labels->push($start->format('d/m') . ' - ' . $end->format('d/m'));
            }
            $newCustomerPer = [];
            $oldCustomerPer = [];
            $firstOrderDates = Order::select('user_id', DB::raw('MIN(DATE(created_at)) as first_order_date'))
                ->groupBy('user_id')
                ->pluck('first_order_date', 'user_id');
            foreach ($weeks as [$start, $end]) {
                // Khách mới: đơn đầu tiên trong tuần này
                $newCustomerIds = $firstOrderDates->filter(fn($d) => $d >= $start->format('Y-m-d') && $d <= $end->format('Y-m-d'))->keys();
                $newCount = Order::whereIn('user_id', $newCustomerIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
                $newCustomerPer[] = $newCount;

                // Khách cũ: có đơn trong tuần này, nhưng đơn đầu tiên trước tuần này
                $oldCustomerIds = $firstOrderDates->filter(fn($d) => $d < $start->format('Y-m-d'))->keys();
                $oldCount = Order::whereIn('user_id', $oldCustomerIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
                $oldCustomerPer[] = $oldCount;
            }
        } elseif ($type === 'month') {
            $labels = collect();
            $months = [];
            for ($i = 11; $i >= 0; $i--) {
                $start = Carbon::now()->startOfMonth()->subMonths($i);
                $end = $start->copy()->endOfMonth();
                $months[] = [$start->copy(), $end->copy()];
                $labels->push($start->format('m/Y'));
            }
            $newCustomerPer = [];
            $oldCustomerPer = [];
            $firstOrderDates = Order::select('user_id', DB::raw('MIN(DATE(created_at)) as first_order_date'))
                ->groupBy('user_id')
                ->pluck('first_order_date', 'user_id');
            foreach ($months as [$start, $end]) {
                // Khách mới: đơn đầu tiên trong tháng này
                $newCustomerIds = $firstOrderDates->filter(fn($d) => $d >= $start->format('Y-m-d') && $d <= $end->format('Y-m-d'))->keys();
                $newCount = Order::whereIn('user_id', $newCustomerIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
                $newCustomerPer[] = $newCount;

                // Khách cũ: có đơn trong tháng này, nhưng đơn đầu tiên trước tháng này
                $oldCustomerIds = $firstOrderDates->filter(fn($d) => $d < $start->format('Y-m-d'))->keys();
                $oldCount = Order::whereIn('user_id', $oldCustomerIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
                $oldCustomerPer[] = $oldCount;
            }
        } else { // day
            $labels = collect();
            $days = [];
            for ($i = 9; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $days[] = $date->copy();
                $labels->push($date->format('d/m'));
            }
            $newCustomerPer = [];
            $oldCustomerPer = [];
            $firstOrderDates = Order::select('user_id', DB::raw('MIN(DATE(created_at)) as first_order_date'))
                ->groupBy('user_id')
                ->pluck('first_order_date', 'user_id');
            foreach ($days as $date) {
                // Khách mới: đơn đầu tiên đúng ngày này
                $newCustomerIds = $firstOrderDates->filter(fn($d) => $d == $date->format('Y-m-d'))->keys();
                $newCount = Order::whereIn('user_id', $newCustomerIds)
                    ->whereDate('created_at', $date->format('Y-m-d'))
                    ->count();
                $newCustomerPer[] = $newCount;

                // Khách cũ: có đơn trong ngày này, nhưng đơn đầu tiên trước ngày này
                $oldCustomerIds = $firstOrderDates->filter(fn($d) => $d < $date->format('Y-m-d'))->keys();
                $oldCount = Order::whereIn('user_id', $oldCustomerIds)
                    ->whereDate('created_at', $date->format('Y-m-d'))
                    ->count();
                $oldCustomerPer[] = $oldCount;
            }
        }

        return response()->json([
            'repeat_customer_labels' => $labels,
            'repeat_customer_new' => $newCustomerPer,
            'repeat_customer_old' => $oldCustomerPer,
        ]);
    }
}
