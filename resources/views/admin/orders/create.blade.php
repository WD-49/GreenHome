@extends('layouts.admin')
@section('title', 'Tạo đơn hàng mới')
@section('content')
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Tạo đơn hàng mới</h3>
            </div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.orders.store') }}" method="POST">
                    @csrf

                    <!-- Người đặt hàng -->
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Người đặt hàng</label>
                        <select name="user_id" class="form-select" >
                            <option value="">-- Chọn người dùng --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Thông tin người nhận -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="shipping_name" class="form-label">Tên người nhận</label>
                            <input type="text" name="shipping_name" class="form-control" >
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="shipping_phone" class="form-label">Số điện thoại</label>
                            <input type="text" name="shipping_phone" class="form-control" >
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="shipping_address" class="form-label">Địa chỉ</label>
                            <input type="text" name="shipping_address" class="form-control" >
                        </div>
                    </div>

                    <!-- Sản phẩm -->
                    <div class="mb-3">
                        <label class="form-label">Chọn sản phẩm</label>
                        <div id="products">
                            <div class="row product-item mb-2">
                                <div class="col-md-6">
                                    <select name="products[]" class="form-select">
                                        @foreach ($productVariants as $variant)
                                            <option value="{{ $variant->id }}">
                                                {{ $variant->product->name }} ({{ $variant->sku }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="quantities[]" class="form-control" placeholder="Số lượng"
                                        min="1" >
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addProductRow()">+ Thêm sản
                            phẩm</button>
                    </div>

                    <!-- Giảm giá -->
                    <div class="mb-3">
                        <label for="discount_id" class="form-label">Mã giảm giá (nếu có)</label>
                        <select name="discount_id" class="form-select">
                            <option value="">-- Không áp dụng --</option>
                            @foreach ($discounts as $discount)
                                <option value="{{ $discount->id }}">{{ $discount->code }} ({{ $discount->description }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Thanh toán -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="payment_method_id" class="form-label">Phương thức thanh toán</label>
                            <select name="payment_method_id" class="form-select" >
                                @foreach ($payMethods as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="shipping_fee" class="form-label">Phí vận chuyển</label>
                            <input type="number" name="shipping_fee" class="form-control"  min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="1"></textarea>
                        </div>
                    </div>

                    <!-- Nút submit -->
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Tạo đơn hàng</button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function addProductRow() {
            const productsDiv = document.getElementById('products');
            const productItem = document.querySelector('.product-item').cloneNode(true);
            productItem.querySelector('select').selectedIndex = 0;
            productItem.querySelector('input[type="number"]').value = '';
            productsDiv.appendChild(productItem);
        }
    </script>
@endsection
