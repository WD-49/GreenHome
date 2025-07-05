<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'dashboard';
        return view('admin.dashboard', compact('title'));
    }

    public function data()
    {
        $to = request('to') ? Carbon::parse(request('to')) : Carbon::today();
        $from = request('from') ? Carbon::parse(request('from')) : $to->copy()->subDays(10);

        $dates = collect();
        $diff = $from->diffInDays($to);
        for ($i = 0; $i <= $diff; $i++) {
            $dates->push($from->copy()->addDays($i)->format('Y-m-d'));
        }

        // Đơn hàng hôm nay và hôm qua
        $ordersToday = Order::whereDate('created_at', $to)->count();
        $ordersYesterday = Order::whereDate('created_at', $to->copy()->subDay())->count();

        $ordersPerDay = Order::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');
        $ordersLastDays = $dates->map(fn($date) => (int)($ordersPerDay[$date] ?? 0));
        $ordersLabels = $dates->map(fn($d) => Carbon::parse($d)->format('d/m'));

        // Doanh thu hôm nay và hôm qua
        $salesToday = Order::whereDate('created_at', $to)->where('payment_status', 'paid')->sum('total_amount');
        $salesYesterday = Order::whereDate('created_at', $to->copy()->subDay())->where('payment_status', 'paid')->sum('total_amount');

        $salesPerDay = Order::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->where('payment_status', 'paid')
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');
        $salesLastDays = $dates->map(fn($date) => (float)($salesPerDay[$date] ?? 0));

        // Khách hàng mới hôm nay và hôm qua
        $newCustomersToday = User::whereDate('created_at', $to)->count();
        $newCustomersYesterday = User::whereDate('created_at', $to->copy()->subDay())->count();
        $newCustomersPercent = $newCustomersYesterday == 0
            ? ($newCustomersToday > 0 ? 100 : 0)
            : round((($newCustomersToday - $newCustomersYesterday) / $newCustomersYesterday) * 100, 2);

        $customersPerDay = User::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');
        $newCustomersLastDays = $dates->map(fn($date) => (int)($customersPerDay[$date] ?? 0));
        $newCustomersLabels = $dates->map(fn($d) => Carbon::parse($d)->format('d/m'));

        // Tổng số sản phẩm đã bán hôm nay và hôm qua
        $validOrderIdsToday = Order::whereDate('created_at', $to)
            ->where('order_status', '!=', 'Hủy đơn')
            ->pluck('id');
        $validOrderIdsYesterday = Order::whereDate('created_at', $to->copy()->subDay())
            ->where('order_status', '!=', 'Hủy đơn')
            ->pluck('id');
        $totalProductsSoldToday = OrderItem::whereIn('order_id', $validOrderIdsToday)->sum('quantity');
        $totalProductsSoldYesterday = OrderItem::whereIn('order_id', $validOrderIdsYesterday)->sum('quantity');
        $totalProductsSoldPercent = $totalProductsSoldYesterday == 0
            ? ($totalProductsSoldToday > 0 ? 100 : 0)
            : round((($totalProductsSoldToday - $totalProductsSoldYesterday) / $totalProductsSoldYesterday) * 100, 2);

        $validOrderIds = Order::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
            ->where('order_status', '!=', 'Hủy đơn')
            ->pluck('id');
        $productsSoldPerDay = OrderItem::whereIn('order_id', $validOrderIds)
            ->selectRaw('DATE(created_at) as date, SUM(quantity) as total')
            ->groupBy('date')
            ->pluck('total', 'date');
        $totalProductsSoldLastDays = $dates->map(fn($date) => (int)($productsSoldPerDay[$date] ?? 0));
        $totalProductsSoldLabels = $dates->map(fn($d) => Carbon::parse($d)->format('d/m'));

        return response()->json([
            'orders_today' => $ordersToday,
            'orders_yesterday' => $ordersYesterday,
            'orders_percent_change' => $ordersYesterday == 0 ? ($ordersToday > 0 ? 100 : 0) : round((($ordersToday - $ordersYesterday) / $ordersYesterday) * 100, 2),
            'orders_last_7_days' => $ordersLastDays,
            'orders_last_7_days_labels' => $ordersLabels,

            'sales_today' => $salesToday,
            'sales_yesterday' => $salesYesterday,
            'sales_percent_change' => $salesYesterday == 0 ? ($salesToday > 0 ? 100 : 0) : round((($salesToday - $salesYesterday) / $salesYesterday) * 100, 2),
            'sales_last_7_days' => $salesLastDays,
            'sales_last_7_days_labels' => $ordersLabels,

            'new_customers_today' => $newCustomersToday,
            'new_customers_yesterday' => $newCustomersYesterday,
            'new_customers_percent' => $newCustomersPercent,
            'new_customers_last_7_days' => $newCustomersLastDays,
            'new_customers_last_7_days_labels' => $newCustomersLabels,

            'total_products_sold_today' => $totalProductsSoldToday,
            'total_products_sold_yesterday' => $totalProductsSoldYesterday,
            'total_products_sold_percent' => $totalProductsSoldPercent,
            'total_products_sold_last_7_days' => $totalProductsSoldLastDays,
            'total_products_sold_last_7_days_labels' => $totalProductsSoldLabels,
        ]);
    }

    public function repeatCustomerRate()
    {
        $type = request('range', 'day'); // day, week, month
        $to = request('to') ? Carbon::parse(request('to')) : Carbon::today();
        $from = request('from') ? Carbon::parse(request('from')) : $to->copy()->subDays(9); // 10 ngày tính cả today

        if ($type === 'week') {
            $labels = collect();
            $weeks = [];
            for ($i = 7; $i > 0; $i--) {
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
                $newCustomerIds = $firstOrderDates->filter(fn($d) => $d >= $start->format('Y-m-d') && $d <= $end->format('Y-m-d'))->keys();
                $newCount = Order::whereIn('user_id', $newCustomerIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
                $newCustomerPer[] = $newCount;

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
                $newCustomerIds = $firstOrderDates->filter(fn($d) => $d >= $start->format('Y-m-d') && $d <= $end->format('Y-m-d'))->keys();
                $newCount = Order::whereIn('user_id', $newCustomerIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
                $newCustomerPer[] = $newCount;

                $oldCustomerIds = $firstOrderDates->filter(fn($d) => $d < $start->format('Y-m-d'))->keys();
                $oldCount = Order::whereIn('user_id', $oldCustomerIds)
                    ->whereBetween('created_at', [$start, $end])
                    ->count();
                $oldCustomerPer[] = $oldCount;
            }
        } else { // day
            $labels = collect();
            $days = [];

            $rangeStart = $from ?? Carbon::today()->subDays(9);
            $rangeEnd = $to ?? Carbon::today();

            for ($date = $rangeStart->copy(); $date <= $rangeEnd; $date->addDay()) {
                $days[] = $date->copy();
                $labels->push($date->format('d/m'));
            }

            $newCustomerPer = [];
            $oldCustomerPer = [];
            $firstOrderDates = Order::select('user_id', DB::raw('MIN(DATE(created_at)) as first_order_date'))
                ->groupBy('user_id')
                ->pluck('first_order_date', 'user_id');
            foreach ($days as $date) {
                $newCustomerIds = $firstOrderDates->filter(fn($d) => $d == $date->format('Y-m-d'))->keys();
                $newCount = Order::whereIn('user_id', $newCustomerIds)
                    ->whereDate('created_at', $date->format('Y-m-d'))
                    ->count();
                $newCustomerPer[] = $newCount;

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



    public function topSellingProducts(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $validOrderIds = Order::whereNotIn('order_status', ['Hủy đơn', 'Chưa xác nhận']);

        if ($from && $to) {
            $validOrderIds = $validOrderIds->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        } else {
            $toDate = Carbon::today();
            $fromDate = $toDate->copy()->subDays(10);
            $validOrderIds = $validOrderIds->whereBetween('created_at', [$fromDate->startOfDay(), $toDate->endOfDay()]);
        }

        $validOrderIds = $validOrderIds->pluck('id');

        $products = OrderItem::whereIn('order_id', $validOrderIds)
            ->leftJoin('product_variants', 'order_items.product_variant_sku', '=', 'product_variants.sku')
            ->select(
                'order_items.product_name',
                'order_items.product_variant_sku as product_sku',
                'order_items.product_attribute',
                DB::raw('MAX(order_items.unit_price) as product_price'),
                DB::raw('SUM(order_items.quantity) as sold'),
                'product_variants.image'
            )
            ->groupBy(
                'order_items.product_name',
                'order_items.product_variant_sku',
                'order_items.product_attribute',
                'product_variants.image'
            )
            ->orderByDesc('sold')
            ->limit(4)
            ->get();

        return response()->json($products);
    }



    public function salesReportIncome(Request $request)
    {
        $year = $request->query('year', Carbon::now()->year);
        $from = $request->query('from');
        $to = $request->query('to');

        $years = Order::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Nếu có từ ngày đến ngày -> tính theo ngày
        if ($from && $to) {
            $labels = collect();
            $incomeData = collect();

            // Lấy doanh thu từng ngày trong khoảng from - to
            $salesPerDay = Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('total', 'date');

            // Tạo mảng ngày liên tục từ $from đến $to làm labels
            $period = \Carbon\CarbonPeriod::create($from, $to);
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $labels->push($date->format('d-m'));
                $incomeData->push((float) ($salesPerDay[$dateStr] ?? 0));
            }
        } else {
            // Nếu không có from/to thì lấy theo năm, theo tháng
            $labels = collect(range(1, 12))->map(fn($m) => 'Tháng ' . $m);

            $salesPerMonth = Order::where('payment_status', 'paid')
                ->whereYear('created_at', $year)
                ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
                ->groupBy('month')
                ->pluck('total', 'month');

            $incomeData = collect(range(1, 12))->map(fn($m) => (float)($salesPerMonth[$m] ?? 0));
        }

        return response()->json([
            'labels' => $labels,
            'income' => $incomeData,
            'years' => $years,
            'selected_year' => (int)$year,
        ]);
    }
}
