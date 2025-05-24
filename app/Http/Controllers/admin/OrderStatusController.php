<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\order\status\StoreOrderStatusRequest;
use App\Http\Requests\admin\order\status\UpdateOrderStatusRequest;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

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
    public function destroy($id)
    {
        $item = OrderStatus::findOrFail($id);
        $item->delete();
        return redirect()->route('admin.orders.status.index')->with('success', 'Xóa trạng thái thành công!');
    }
    public function trashed() {
        $statuses = OrderStatus::onlyTrashed()->get();
        $title = "Trạng thái đã xóa";
        return view('admin.orders.status.trash', compact('statuses', 'title'));
    }
    public function restore($id) {
        $data = OrderStatus::onlyTrashed()->findOrFail($id);
        $data->restore();
        return redirect()->route('admin.orders.status.index')->with('success', 'Khôi phục trạng thái thành công!');
    }
}
