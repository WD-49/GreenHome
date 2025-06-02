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
        $deleteCount = OrderStatus::onlyTrashed()->count();
        $statuses = OrderStatus::all();
        return view('admin.orders.status.index', compact('title', 'statuses', 'deleteCount'));
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
    public function destroy($id) {
        $order = OrderStatus::findOrFail($id);
        $order->delete();
        return redirect()->route('admin.orders.status.index')->with('success', 'Xóa thành công trạng thái thành công!');
    }
    public function trashed() {
        $data = OrderStatus::all();
        $statuses = OrderStatus::onlyTrashed()->get();
        $deleteCount = OrderStatus::onlyTrashed()->count();
        return view('admin.orders.status.trashed', compact('statuses', 'deleteCount', 'data'));
    }
    public function restore($id) {
        $status = OrderStatus::onlyTrashed()->findOrFail($id);
        $status->restore();
        return redirect()->route('admin.orders.status.index')->with('success', 'Khôi phục trạng thái thành công!');
    }
    
}
