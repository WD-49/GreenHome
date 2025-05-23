<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\order\status\StoreOrderStatusRequest;
use App\Http\Requests\admin\order\status\UpdateOrderStatusRequest;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function index()
    {
        $title = "Trạng thái đơn hàng";
        $statuses = OrderStatus::all();
        return view('admin.orders.status.index', compact('title', 'statuses'));
    }
    public function create()
    {
        $title = "Thêm mới trạng thái";
        return view('admin.orders.status.create', compact('title'));
    }
    public function store(StoreOrderStatusRequest $request)
    {
        $data = $request->validated();
        OrderStatus::create(
            [
                'name' => $data['name']
            ]
        );
        return redirect()->route('admin.orders.status.index')->with('success', 'Thêm trạng thái thành công!');
    }
    public function edit($id)
    {
        $status = OrderStatus::findOrFail($id);
        $title = "Sửa trạng thái đơn hàng";
        return view('admin.orders.status.edit', compact('title', 'status'));
    }
    public function update($id, UpdateOrderStatusRequest $request)
    {
        $status = OrderStatus::findOrFail($id);
        $data = $request->validated();
        $status->update([
            'name' => $data['name']
        ]);
        return redirect()->route('admin.orders.status.index')->with('success', 'Cập nhật trạng thái thành công!');
    }
}
