@extends('layouts.admin')
@section('title', 'Thùng rác đánh giá sản phẩm')

@section('content')
    <h1 class="mb-4">Thùng rác đánh giá sản phẩm</h1>

   
    <!-- HÀNG CHỨA NÚT BỘ LỌC -->
<div class="d-flex justify-content-between align-items-center mb-4 gap-2 flex-wrap">
    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>

    <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterForm" aria-expanded="false">
        <i class="bi bi-funnel"></i> Bộ lọc nâng cao
    </button>
</div>

<!-- FORM BỘ LỌC -->
<div class="collapse mt-3" id="filterForm">
    <form action="" method="GET" class="row g-3">
        <!-- LỌC THEO SỐ SAO -->
        <div class="col-md-3">
            <label for="rating" class="form-label">Số sao</label>
            <select name="rating" id="rating" class="form-select">
                <option value="">-- Tất cả --</option>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} sao</option>
                @endfor
            </select>
        </div>

        <!-- LỌC THEO TRẠNG THÁI (TRƯỚC KHI BỊ XÓA) -->
        <div class="col-md-3">
            <label for="status" class="form-label">Trạng thái trước khi xóa</label>
            <select name="status" id="status" class="form-select">
                <option value="">-- Tất cả --</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Hiển thị</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>

        <div class="col-md-12 mt-2">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            <a href="{{ url()->current() }}" class="btn btn-secondary">Xóa bộ lọc</a>
        </div>
    </form>
</div><br><br>


    <div class="table-responsive-md">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Biến thể</th>
                    <th>Người dùng</th>
                    <th>Đánh giá</th>
                    <th>Tiêu đề</th>
                    <th>Ngày xóa</th>
                    <TH>Trạng thái</TH>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $index => $review)
                    <tr>
                        <td>{{ $reviews->firstItem() + $index }}</td>
                        <td>{{ $review->productVariant->sku ?? 'N/A' }}</td>
                        <td>{{ $review->user->name ?? 'N/A' }}</td>
                        <td class="text-warning">
                            {!! str_repeat('★', $review->rating) !!}
                        </td>
                        <td>{{ $review->title ?? 'Không có tiêu đề' }}</td>
                        <td>{{ $review->deleted_at ? $review->deleted_at->format('d/m/Y') : 'Không rõ' }}</td>
                        <td>
    @if ($review->status == 'approved')
        <span class="badge bg-success">Hiển thị</span>
    @elseif ($review->status == 'pending')
        <span class="badge bg-warning text-dark">Chờ duyệt</span>
    @elseif ($review->status == 'rejected')
        <span class="badge bg-danger">Ẩn</span>
    @else
        <span class="badge bg-secondary">Không rõ</span>
    @endif
</td>

                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                {{-- Khôi phục --}}
                                <form action="{{ route('admin.reviews.restore', $review->id) }}" method="POST"
                                      onsubmit="return confirm('Khôi phục đánh giá này?');">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </form>

                                {{-- Xóa vĩnh viễn --}}
                                <form action="{{ route('admin.reviews.forceDelete', $review->id) }}" method="POST"
                                      onsubmit="return confirm('Xóa vĩnh viễn đánh giá này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Không có đánh giá nào trong thùng rác.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $reviews->links() }}
    </div>
@endsection
