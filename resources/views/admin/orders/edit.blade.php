@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-warning text-white">
                <h3 class="mb-0">Sửa đơn hàng #{{ $order->id }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Tên người nhận --}}
                    <div class="mb-3">
                        <label class="form-label">Tên người nhận</label>
                        <input type="text" name="shipping_name" class="form-control"
                            value="{{ old('shipping_name', $order->shipping_name) }}">
                        @error('shipping_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Số điện thoại --}}
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="text" name="shipping_phone" class="form-control"
                            value="{{ old('shipping_phone', $order->shipping_phone) }}">
                        @error('shipping_phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Địa chỉ --}}
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="shipping_address" class="form-control"
                            value="{{ old('shipping_address', $order->shipping_address) }}">
                        @error('shipping_address')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Trạng thái --}}
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status_id" class="form-select">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}"
                                    {{ $order->status_id == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('status_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Tổng tiền --}}
                    <div class="mb-3">
                        <label class="form-label">Tổng tiền</label>
                        <input type="text" name="total_amount" class="form-control"
                            value="{{ old('total_amount', $order->total_amount) }}">
                        @error('total_amount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Ghi chú --}}
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="note" class="form-control" rows="3">{{ old('note', $order->note) }}</textarea>
                        @error('note')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Quay lại</a>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
