@extends('layouts.admin')
@section('title', 'Quản lý đánh giá sản phẩm')

@section('content')
    <style>
        <style>.image-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            max-width: 220px;
            /* Giới hạn chiều rộng để ảnh không tràn */
        }

        .image-grid img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            transition: transform 0.2s ease;
        }

        .image-grid img:hover {
            transform: scale(1.05);
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.2);
        }
    </style>

    </style>
    <h1 class="mb-4">Quản lý đánh giá sản phẩm</h1>
    <!-- HÀNG CHỨA 2 NÚT -->
    <div class="d-flex justify-content-between align-items-center mb-4 gap-2 flex-wrap">
        {{-- <a href="{{ route('admin.reviews.trash') }}" class="btn btn-outline-danger">
        <i class="bi bi-trash3"></i> Xem thùng rác
    </a> --}}

        <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#filterForm"
            aria-expanded="false">
            <i class="bi bi-funnel"></i> Bộ lọc nâng cao
        </button>
    </div>


    <div class="collapse mt-3" id="filterForm">
        <form action="" method="GET" class="row g-3">
            <!-- LỌC THEO SỐ SAO -->
            <div class="col-md-3">
                <label for="rating" class="form-label">Số sao</label>
                <select name="rating" id="rating" class="form-select">
                    <option value="">-- Tất cả --</option>
                    @for ($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} sao</option>
                    @endfor
                </select>
            </div>

            <!-- LỌC THEO TRẠNG THÁI -->
            <div class="col-md-3">
                <label for="status" class="form-label">Trạng thái</label>
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
    </div>
    </div>
    <br> <br>

    <div class="table-responsive-md">
        <table class="table table-bordered align-middle text-center">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Sản phẩm</th>
                    <th>Biến thể</th>
                    <th>Người dùng</th>
                    <th>Đánh giá</th>
                    <th>Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Ngày tạo</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                    <th>Thay đổi trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $index => $review)
                    <tr>
                        <td>{{ $reviews->firstItem() + $index }}</td>
                        <td><a
                                href="{{ route('admin.products.show', $review->productVariant->product->id) }}">{{ $review->productVariant->product->name ?? 'N/A' }}</a>
                        <td>{{ $review->productVariant->sku ?? 'N/A' }}
                        <td>{{ $review->user->name ?? 'N/A' }}</td>
                        <td class="text-warning">
                            {!! str_repeat('★', $review->rating) !!}
                        </td>
                        <td>
                            @if ($review->images->count() > 0)
                                <div class="image-grid">
                                    @foreach ($review->images as $image)
                                        @php
                                            $imageUrl = Storage::url(str_replace(["\r", "\n"], '', $image->image));
                                        @endphp
                                        <a href="{{ $imageUrl }}" data-lightbox="review-{{ $review->id }}">
                                            <img src="{{ $imageUrl }}" alt="Ảnh đánh giá">
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                Không có ảnh
                            @endif
                        </td>


                        <td>{{ $review->title ?? 'Không có tiêu đề' }}</td>
                        <td>
                            {{ $review->created_at ? $review->created_at->format('d/m/Y') : 'Không có' }}
                        </td>
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
                                <a href="{{ route('admin.reviews.show', $review->id) }}"
                                    class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-eye"></i>
                                </a>

                                {{-- <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form> --}}
                            </div>
                        </td>
                        <td>
                            @if (in_array($review->status, ['pending', 'rejected']))
                                <form action="{{ route('admin.reviews.updateStatus', $review->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn duyệt đánh giá này không?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="approved">
                                    <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                </form>
                            @elseif ($review->status === 'approved')
                                <form action="{{ route('admin.reviews.updateStatus', $review->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn có chắc muốn ẩn đánh giá này không?');">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-warning">Ẩn</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">Không có đánh giá nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $reviews->links() }}
    </div>

@endsection
