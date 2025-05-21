<?php

namespace App\Http\Controllers\admin;

use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'discount', 'items', 'status', 'paymentMethod'])->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'discount', 'items', 'status', 'paymentMethod'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    public function edit($id)
    {
        $order = Order::with(['user', 'discount', 'items', 'status', 'paymentMethod'])->findOrFail($id);
        $statuses = OrderStatus::all();
        return view('orders.edit', compact('order', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:15',
            'shipping_address' => 'required|string|max:255',
            'status_id' => 'required|exists:order_statuses,id',
            'total_amount' => 'required|numeric',
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

        return redirect()->route('orders.index')->with('success', 'Cập nhật đơn hàng thành công!');
    }

    public function trash()
    {
        $orders = Order::onlyTrashed()->withDetails()->get();
        return view('orders.trash', compact('orders'));
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
        return redirect()->route('orders.trash')->with('success', 'Khôi phục đơn hàng thành công!');
    }

    public function forceDelete($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->forceDelete();
        return redirect()->route('orders.trash')->with('success', 'Đã xóa vĩnh viễn đơn hàng!');
    }
}
