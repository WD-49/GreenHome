@extends('layouts.admin')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Danh sách đơn hàng</h1>
        <table class="min-w-full bg-white border">
            <thead>
                <tr>
                    <th class="border px-4 py-2">STT</th>
                    <th class="border px-4 py-2">Mã đơn hàng</th>
                    <th class="border px-4 py-2">Tên người đặt</th>
                    <th class="border px-4 py-2">Ngày đặt</th>
                    <th class="border px-4 py-2">Tổng tiền</th>
                    <th class="border px-4 py-2">Trạng thái</th>
                    <th class="border px-4 py-2">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $index => $order)
                    <tr>
                        <td class="border px-4 py-2">{{ $index + 1 }}</td>
                        <td class="border px-4 py-2">{{ $order->id }}</td>
                        <td class="border px-4 py-2">{{ $order->shipping_name }}</td>
                        <td class="border px-4 py-2">{{ $order->created_at }}</td>
                        <td class="border px-4 py-2">{{ number_format($order->total_amount, 2) }} VND</td>
                        <td class="border px-4 py-2">{{ $order->status->name }}</td>
                        <td class="border px-4 py-2 d-flex">
                            <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-warning">Sửa</a>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info">Xem chi tiết</a>
                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Bạn có chắc muốn xóa?')">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- <a href="{{ route('orders.trash') }}" class="mt-4 inline-block bg-gray-500 text-white px-4 py-2">Xem thùng rác</a> --}}
    </div>
@endsection
