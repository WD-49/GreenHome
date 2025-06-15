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
        $salesToday = Order::whereDate('created_at', $today)->sum('total_amount');
        $yesterday = Carbon::yesterday();
        $salesYesterday = Order::whereDate('created_at', $yesterday)->sum('total_amount');



        // Đơn hàng hôm nay & hôm qua
        $ordersToday = Order::whereDate('created_at', $today)->count();
        $ordersYesterday = Order::whereDate('created_at', $yesterday)->count();

        // Dữ liệu 7 ngày gần nhất
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dates->push($date->format('Y-m-d'));
        }
        // dd($dates);


        $ordersPerDay = Order::whereBetween('created_at', [Carbon::today()->subDays(6), Carbon::today()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $ordersLast7Days = $dates->map(function ($date) use ($ordersPerDay) {
            return $ordersPerDay->get($date, 0);
        });

        // doanh thu hôm nay & hôm qua
        $salesPerday = Order::whereBetween('created_at', [Carbon::today()->subDays(6), Carbon::today()->endOfDay()])
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


        ]);
    }
}
