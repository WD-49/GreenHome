@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Danh sách đơn hàng</h3>
            </div>
            <a href="{{ route('admin.orders.trash') }}" class="btn btn-secondary mt-3">Xem thùng rác</a>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">Mã đơn hàng</th>
                            <th scope="col">Tên người đặt</th>
                            <th scope="col">Ngày đặt</th>
                            <th scope="col">Tổng tiền</th>
                            <th scope="col">Trạng thái</th>
                            <th scope="col">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $index => $order)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->shipping_name }}</td>
                                <td>{{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                <td>{{ number_format($order->total_amount, 0) }} VND</td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $order->status->name ?? 'Chưa cập nhật' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('admin.orders.edit', $order->id) }}"
                                            class="btn btn-sm btn-warning">Sửa</a>
                                        <a href="{{ route('admin.orders.show', $order->id) }}"
                                            class="btn btn-sm btn-info text-white">Xem</a>
                                        <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                
            </div>
        </div>
    </div>
@endsection
