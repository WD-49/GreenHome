<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use App\Models\OrderItem;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    use Filterable;

    public function index(Request $request)
    {
        $filterData = $this->applyFilter($request, 'day');
        $filter = $filterData['filter'];
        $startDate = $filterData['start_date'];
        $endDate = $filterData['end_date'];

        return view('admin.dashboard', compact('filter', 'startDate', 'endDate'));
    }

    public function getDashboardData(Request $request)
    {
        try {
            $filterData = $this->applyFilter($request, 'day');
            $filter = $filterData['filter'];
            $startDate = $filterData['start_date'];
            $endDate = $filterData['end_date'];
            $groupBy = $filterData['group_by'];
            $interval = $filterData['interval'];

            // New Orders
            $newOrders = Order::selectRaw("{$groupBy} as date, COUNT(*) as count")
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupByRaw($groupBy)
                ->orderByRaw($groupBy)
                ->get();

            // Sales
            $sales = Order::selectRaw("{$groupBy} as date, COUNT(*) as count")
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('order_status', 'Đã nhận hàng')
                ->where('payment_status', 'paid')
                ->groupByRaw($groupBy)
                ->orderByRaw($groupBy)
                ->get();

            // Revenue
            $revenue = Order::selectRaw("{$groupBy} as date, SUM(total_amount) as total")
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('order_status', 'Đã nhận hàng')
                ->where('payment_status', 'paid')
                ->groupByRaw($groupBy)
                ->orderByRaw($groupBy)
                ->get();

            // New Users
            $newUsers = User::selectRaw("{$groupBy} as date, COUNT(*) as count")
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', true)
                ->whereNull('deleted_at')
                ->groupByRaw($groupBy)
                ->orderByRaw($groupBy)
                ->get();

            // Top Customers
            $topCustomers = Order::select(
                'users.name',
                DB::raw('COUNT(orders.id) as order_count'),
                DB::raw('SUM(orders.total_amount) as total_spent')
            )
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->where('orders.order_status', 'Đã nhận hàng')
                ->where('orders.payment_status', 'paid')
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('order_count')
                ->take(10)
                ->get();

            // Top Selling Products
            $validOrderIds = Order::where('order_status', 'Đã nhận hàng')
                ->where('payment_status', 'paid')
                ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->pluck('id');

            $topSellingProducts = OrderItem::whereIn('order_id', $validOrderIds)
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

            // Top Rated Products
            $topRatedProducts = Review::query()
                ->join('product_variants', 'reviews.product_variant_id', '=', 'product_variants.id')
                ->join('products', 'product_variants.product_id', '=', 'products.id')
                ->select(
                    'products.name as product_name',
                    'product_variants.sku as product_sku',
                    'product_variants.attribute_name as product_attribute',
                    'product_variants.image',
                    DB::raw('COALESCE(AVG(reviews.rating), 0) as rating'),
                    DB::raw('COUNT(reviews.id) as review_count')
                )
                ->where('reviews.status', 'approved')
                ->whereNotNull('reviews.order_item_id')
                ->whereBetween('reviews.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->groupBy(
                    'products.name',
                    'product_variants.sku',
                    'product_variants.attribute_name',
                    'product_variants.image'
                )
                ->orderByDesc('rating')
                ->take(5)
                ->get();

            // Current Orders
            $currentOrders = Order::select(
                'orders.id',
                'orders.sku',
                'users.name as user_name',
                DB::raw("COALESCE(user_profiles.user_image, '/images/default-avatar.png') as user_image"),
                'orders.total_amount',
                'orders.order_status',
                'orders.payment_status',
                'orders.created_at'
            )
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->orderByDesc('orders.created_at')
                ->take(10)
                ->get()
                ->map(function ($order) {
                    $order->payment_status_translated = [
                        'pending' => 'Chờ thanh toán',
                        'paid' => 'Đã thanh toán',
                        'failed' => 'Thanh toán thất bại'
                    ][$order->payment_status] ?? $order->payment_status;
                    return $order;
                });

            // Đồng bộ dữ liệu cho biểu đồ
            $labels = $this->generateLabels($startDate, $endDate, $filter, $interval);
            $newOrdersData = [];
            $salesData = [];
            $revenueData = [];
            $newUsersData = [];

            foreach ($labels as $label) {
                $newOrderRecord = $newOrders->where('date', $label['key'])->first();
                $salesRecord = $sales->where('date', $label['key'])->first();
                $revenueRecord = $revenue->where('date', $label['key'])->first();
                $newUsersRecord = $newUsers->where('date', $label['key'])->first();

                $newOrdersData[] = $newOrderRecord ? $newOrderRecord->count : 0;
                $salesData[] = $salesRecord ? $salesRecord->count : 0;
                $revenueData[] = $revenueRecord ? $revenueRecord->total : 0;
                $newUsersData[] = $newUsersRecord ? $newUsersRecord->count : 0;
            }

            $data = [
                'labels' => array_column($labels, 'label'),
                'new_orders' => [
                    'total' => $newOrders->sum('count') ?? 0,
                    'data' => $newOrdersData,
                    'empty' => $newOrders->isEmpty(),
                ],
                'sales' => [
                    'total' => $sales->sum('count') ?? 0,
                    'data' => $salesData,
                    'empty' => $sales->isEmpty(),
                ],
                'revenue' => [
                    'total' => $revenue->sum('total') ?? 0,
                    'data' => $revenueData,
                    'empty' => $revenue->isEmpty(),
                ],
                'new_users' => [
                    'total' => $newUsers->sum('count') ?? 0,
                    'data' => $newUsersData,
                    'empty' => $newUsers->isEmpty(),
                ],
                'top_customers' => $topCustomers->toArray(),
                'top_selling_products' => $topSellingProducts->toArray(),
                'top_rated_products' => $topRatedProducts->toArray(),
                'current_orders' => $currentOrders->toArray(),
            ];

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Dashboard data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi server: ' . $e->getMessage(),
            ], 500);
        }
    }
}
