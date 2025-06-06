@extends('layouts.admin')

@section('content')
    <h1>Lịch sử dùng mã giảm giá</h1>
    <form action="" method="GET" class="mb-4 row g-3">
        <div class="col-md-3">
            <label for="code" class="form-label">Mã code</label>
            <input type="text" name="code" id="code" value="{{ request('code') }}" class="form-control"
                placeholder="Nhập mã code">
        </div>

        <div class="col-md-3">
            <label for="user" class="form-label">Người sử dụng</label>
            <input type="text" name="user" id="user" value="{{ request('user') }}" class="form-control"
                placeholder="Tên hoặc email">
        </div>

        <div class="col-md-3">
            <label for="product" class="form-label">Sản phẩm</label>
            <input type="text" name="product" id="product" value="{{ request('product') }}" class="form-control"
                placeholder="Tên sản phẩm">
        </div>

        <div class="col-md-3">
            <label for="order" class="form-label">Đơn hàng</label>
            <input type="text" name="order" id="order" value="{{ request('order') }}" class="form-control"
                placeholder="Mã đơn hàng">
        </div>

        <div class="col-md-3">
            <label for="date_used" class="form-label">Ngày sử dụng</label>
            <input type="date" name="date_used" id="date_used" value="{{ request('date_used') }}" class="form-control">
        </div>

        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            <a href="{{ url()->current() }}" class="btn btn-secondary">Xóa filter</a>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã giảm giá</th>
                <th>Người sử dụng</th>
                <th>Ngày sử dụng</th>
                <th>Sản phẩm</th>
                <th>Mã đơn áp dụng</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usages as $index => $usage)
                <tr>
                    <td>{{ $usages->firstItem() + $index }}</td>
                    <td>{{ $usage->discount->code ?? 'N/A' }}</td>
                    <td>{{ $usage->user->name ?? 'N/A' }}</td>
                    <td>{{ $usage->used_at ?? 'N/A' }}</td>
                    <td>{{ $usage->product->name ?? 'N/A' }}</td>
                    <td>{{ $usage->order->sku ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $usages->links() }} {{-- Phân trang --}}
@endsection
