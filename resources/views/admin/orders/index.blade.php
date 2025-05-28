@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <div class="card shadow border-0">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                <h3 class="mb-0">Danh sách đơn hàng</h3>
                <div>
                    <a href="{{ route('admin.orders.trash') }}" class="btn btn-sm btn-light me-2">
                        <i class="fas fa-trash-alt"></i> Thùng rác
                    </a>
                    <a href="{{ route('admin.orders.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus-circle"></i> Tạo đơn hàng
                    </a>
                </div>
            </div>

            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th scope="col">STT</th>
                                <th scope="col">Mã đơn</th>
                                <th scope="col">Khách hàng</th>
                                <th scope="col">Tên người nhận</th>
                                <th scope="col">Ngày đặt</th>
                                <th scope="col">Tổng tiền</th>
                                <th scope="col">Phương thức</th>
                                <th scope="col">Trạng thái</th>
                                <th scope="col">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $index => $order)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>#{{ $order->sku ?? $order->id }}</td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>{{ $order->shipping_name }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ number_format($order->total_amount, 0) }} VND</td>
                                    <td class="text-capitalize">{{ $order->paymentMethod->name }}</td>
                                    <td>
                                        <span
                                            class="badge 
                                            @if ($order->status->name == 'Hoàn tất') bg-success
                                            @elseif ($order->status->name == 'Đang xử lý') bg-warning text-dark
                                            @else bg-info text-dark @endif">
                                            {{ $order->status->name ?? 'Chưa cập nhật' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.orders.edit', $order->id) }}"
                                                class="btn btn-sm btn-outline-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                                class="btn btn-sm btn-outline-info" title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Không có đơn hàng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
