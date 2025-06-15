<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use App\Models\Order;
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
        // dd($ordersLast7Days);

        return response()->json([
            'orders_today' => $ordersToday,
            'orders_yesterday' => $ordersYesterday,
            'orders_percent_change' => $ordersYesterday == 0 ? ($ordersToday > 0 ? 100 : 0) : round((($ordersToday - $ordersYesterday) / $ordersYesterday) * 100, 2),
            'orders_last_7_days' => $ordersLast7Days,
            'orders_last_7_days_labels' => $dates,
        ]);
    }
}
