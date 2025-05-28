<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Discount;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'discount', 'items', 'status', 'paymentMethod'])->get();
        // dd($orders);
        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $users = User::all();
        $productVariants = ProductVariant::with('product')->get();
        $discounts = Discount::all();
        $payMethods = PaymentMethod::all();

        return view('admin.orders.create', compact('users', 'productVariants', 'discounts', 'payMethods'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:15',
            'shipping_address' => 'required|string|max:255',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:product_variants,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'integer|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'shipping_fee' => 'required|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0|max:99999999',
            'discount_id' => 'nullable|exists:discounts,id',
            'note' => 'nullable|string',
        ], [
            'user_id.required' => 'Vui lòng chọn khách hàng',
            'shipping_name.required' => 'Tên người nhận không được để trống',
            'shipping_phone.required' => 'Số điện thoại không được để trống',
            'shipping_address.required' => 'Địa chỉ không được để trống',
            'products.required' => 'Vui lòng chọn ít nhất 1 sản phẩm',
            'quantities.required' => 'Vui lòng nhập số lượng cho từng sản phẩm',
            'payment_method_id.required' => 'Vui lòng chọn phương thức thanh toán',
            'shipping_fee.required' => 'Vui lòng nhập phí vận chuyển',
        ]);
        // dd($validator);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        return DB::transaction(function () use ($request) {
            $products = $request->input('products');
            $quantities = $request->input('quantities');
            $totalBeforeDiscount = 0;
            $items = [];

            foreach ($products as $index => $variantId) {
                $variant = ProductVariant::findOrFail($variantId);
                $quantity = $quantities[$index];
                $itemTotal = $variant->price * $quantity;
                $totalBeforeDiscount += $itemTotal;

                $items[] = [
                    'product_variant_id' => $variantId,
                    'unit_price' => $variant->price,
                    'total_price' => $itemTotal,
                    'price' => $variant->price,
                    'quantity' => $quantity,
                    'total' => $itemTotal,
                ];
            }

            $discountAmount = 0;
            if ($request->filled('discount_id')) {
                $discount = Discount::findOrFail($request->discount_id);
                $discountAmount = $discount->type === 'percentage'
                    ? $totalBeforeDiscount * $discount->value / 100
                    : $discount->value;
            }

            $totalAmount = max(0, $totalBeforeDiscount - $discountAmount) + $request->shipping_fee;

            do {
                $sku = 'DH' . rand(100, 999) . '-' . rand(1000, 9999);
            } while (Order::where('sku', $sku)->exists());

            $order = Order::create([
                'user_id' => $request->user_id,
                'sku' => $sku,
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'payment_method_id' => $request->payment_method_id,
                'payment_status' => 'pending',
                'status_id' => 1,
                'discount_id' => $request->discount_id,
                'discount_amount' => $discountAmount,
                'shipping_fee' => $request->shipping_fee,
                'total_amount' => $totalAmount,
                'note' => $request->note,
            ]);

            // Tạo nhiều order items cùng lúc thông qua quan hệ
            $order->items()->createMany($items);

            return redirect()->route('admin.orders.index')->with('success', 'Tạo đơn hàng thành công!');
        });
    }



    public function show($id)
    {
        $order = Order::with([
            'user.profile',
            'discount',
            'items.productVariant.product',
            'status',
            'paymentMethod'
        ])->findOrFail($id);
        // dd($order);
        $statuses = OrderStatus::all();

        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status_id = $request->input('status_id');

        // Nếu cập nhật về trạng thái huỷ, yêu cầu lý do
        if ($request->input('status_id') == config('order.statuses.cancelled')) {
            $request->validate([
                'cancel_reason' => 'required|string|max:255',
            ]);
            $order->cancel_reason = $request->input('cancel_reason');
        }

        $order->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thành công!');
    }

    public function edit($id)
    {
        $order = Order::with(['user', 'discount', 'items', 'status', 'paymentMethod'])->findOrFail($id);
        $statuses = OrderStatus::all();
        return view('admin.orders.edit', compact('order', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:15',
            'shipping_address' => 'required|string|max:255',
            'status_id' => 'required|exists:order_statuses,id',
            'total_amount' => 'required|numeric|min:0|max:99999999.99',
            'note' => 'nullable|string',
        ], [
            'shipping_name.required' => 'Tên người nhận không được để trống',
            'shipping_phone.required' => 'Số điện thoại không được để trống',
            'shipping_address.required' => 'Địa chỉ không được để trống',
            'status_id.required' => 'Trạng thái không được để trống',
            'total_amount.required' => 'Tổng tiền không được để trống',
            'total_amount.numeric' => 'Tổng tiền phải là số',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $order = Order::findOrFail($id);
        $order->update($request->only(['shipping_name', 'shipping_phone', 'shipping_address', 'status_id', 'total_amount', 'note']));

        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật đơn hàng thành công!');
    }

    public function trash()
    {
        $orders = Order::onlyTrashed()->with(['user', 'discount', 'items', 'status', 'paymentMethod'])->get();
        return view('admin.orders.trash', compact('orders'));
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->back()->with('success', 'Đã xóa mềm đơn hàng!');
    }

    public function restore($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->restore();
        return redirect()->route('admin.orders.trash')->with('success', 'Khôi phục đơn hàng thành công!');
    }

    public function forceDelete($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->forceDelete();
        return redirect()->route('orders.trash')->with('success', 'Đã xóa vĩnh viễn đơn hàng!');
    }
}
