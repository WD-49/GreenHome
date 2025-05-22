@extends('layouts.admin')

@section('title', 'Chi tiết sản phẩm - ' . $product->name)

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Chi tiết sản phẩm</h2>
            <div>
                <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning btn-sm" title="chỉnh sửa"><i
                        class="fa-solid fa-pen"></i>
                </a>
                @if ($product->trashed())
                    <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button class="btn btn-success" onclick="return confirm('Khôi phục sản phẩm này?')">Khôi
                            phục</button>
                    </form>
                @else
                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm btn-confirm" title="xóa"
                            data-confirm-message="Bạn có chắc chắn muốn bỏ sản phẩm này vào thùng rác không?"><i
                                class="fa-solid fa-trash"></i></button>
                    </form>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Ảnh -->
            <div class="col-md-4">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                        class="img-fluid rounded">
                @else
                    <p>sản phẩm chưa có ảnh</p>
                @endif
            </div>

            <!-- Thông tin -->
            <div class="col-md-8">
                <table class="table table-bordered">
                    <tr>
                        <th>Tên sản phẩm</th>
                        <td>{{ $product->name }}</td>
                    </tr>
                    <tr>
                        <th>Danh mục</th>
                        <td>{{ $product->category->name ?? 'Chưa có' }}</td>
                    </tr>
                    <tr>
                        <th>Thương hiệu</th>
                        <td>{{ $product->brand->name ?? 'Chưa có' }}</td>
                    </tr>
                    <tr>
                        <th>Giá gốc</th>
                        <td>${{ number_format($product->price, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Giá khuyến mãi</th>
                        <td>
                            @if ($product->promotional_price)
                                ${{ number_format($product->promotional_price, 2) }}
                            @else
                                <em>Không có</em>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Số lượng</th>
                        <td>{{ $product->quantity }}</td>
                    </tr>
                    <tr>
                        <th>Ngày nhập</th>
                        <td>{{ optional($product->date_of_entry)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>
                            @if ($product->status)
                                <span class="badge bg-success">Đang bán</span>
                            @else
                                <span class="badge bg-secondary">dừng bán</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Lượt xem</th>
                        <td>{{ $product->view }}</td>
                    </tr>
                    <tr>
                        <th>Mô tả</th>
                        <td>{!! nl2br(e($product->description)) !!}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
