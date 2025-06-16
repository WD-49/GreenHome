@extends('layouts.admin')

@section('content')
    <h1 class="mb-4">Lịch sử dùng mã giảm giá</h1>

    <!-- BỘ LỌC NÂNG CAO (Collapse) -->
    <div class="mb-4">
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterForm" aria-expanded="false">
            Bộ lọc nâng cao
        </button>

        <div class="collapse mt-3" id="filterForm">
            <form action="" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="code" class="form-label">Mã code</label>
                    <input type="text" name="code" id="code" value="{{ request('code') }}" class="form-control" placeholder="Nhập mã code">
                </div>

                <div class="col-md-3">
                    <label for="user" class="form-label">Người sử dụng</label>
                    <input type="text" name="user" id="user" value="{{ request('user') }}" class="form-control" placeholder="Tên hoặc email">
                </div>

                {{-- <div class="col-md-3">
                    <label for="product" class="form-label">Sản phẩm</label>
                    <input type="text" name="product" id="product" value="{{ request('product') }}" class="form-control" placeholder="Tên sản phẩm">
                </div> --}}

                <div class="col-md-3">
                    <label for="order" class="form-label">Đơn hàng</label>
                    <input type="text" name="order" id="order" value="{{ request('order') }}" class="form-control" placeholder="Mã đơn hàng">
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
        </div>
    </div>

    <!-- BẢNG DỮ LIỆU -->
  <div class="table-responsive-md" style="overflow: visible;">

        <table class="table table-bordered align-middle text-center">
            <thead class="table-light" >
                <tr>
                    <th>STT</th>
                    <th>Mã giảm giá</th>
                    <th>Người sử dụng</th>
                    <th>Ngày sử dụng</th>
                    <th>Mã đơn áp dụng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usages as $index => $usage)
                    <tr>
                        <td>{{ $usages->firstItem() + $index }}</td>
                        <td>{{ $usage->discount_code ?? 'N/A' }}</td>
                        <td>{{ $usage->user_name ?? 'N/A' }}</td>
                        <td>{{ $usage->used_at ?? 'N/A' }}</td>
                        <td>{{ $usage->order->sku ?? 'N/A' }}</td>
                     <td class="text-center">
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-three-dots-vertical"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li>
                <a href="{{ route('admin.discount.historyDetail', $usage->id) }}" class="dropdown-item">
                    <i class="bi bi-eye text-primary me-2"></i> Xem
                </a>
            </li>
           
            <li>
                {{-- <form action="" method="POST" onsubmit="return confirm('Bạn có muốn xóa mã này không?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item">
                        <i class="bi bi-trash text-danger me-2"></i> Xóa
                    </button>
                </form> --}}
            </li>
        </ul>
    </div>
</td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Không có dữ liệu</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PHÂN TRANG -->
    <div class="mt-3">
        {{ $usages->links() }}
    </div>
@endsection
