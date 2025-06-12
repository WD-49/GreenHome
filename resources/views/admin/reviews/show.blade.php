@extends('layouts.admin')
@section('title', 'Chi tiết đánh giá sản phẩm')
@section('content')
    <style>
        .review-container {
            max-width: 1000px;
        }

        .review-header {
            font-size: 2.2rem;
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 1.5rem;
        }

        .review-card {
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(13, 110, 253, 0.15);
            border: none;
        }

        .review-title {
            font-weight: 600;
            font-size: 1.5rem;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 0.5rem;
            color: #0d6efd;
            margin-bottom: 2rem;
        }

        .review-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .label-text {
            font-weight: 600;
            color: #6c757d;
            min-width: 200px;
        }

        .value-text {
            font-weight: 700;
            color: #212529;
        }

        .btn-group-custom {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
        }

        .btn-custom {
            border-radius: 10px;
            padding: 0.6rem 1.5rem;
            font-weight: 700;
            font-size: 1rem;
        }

        .btn-custom-primary {
            background: linear-gradient(45deg, #0d6efd, #0056b3);
            border: none;
            color: white;
        }

        .btn-custom-primary:hover {
            background: linear-gradient(45deg, #0056b3, #0d6efd);
        }

        .btn-custom-outline {
            border: 2px solid #6c757d;
            color: #6c757d;
        }

        .btn-custom-outline:hover {
            background: #6c757d;
            color: white;
        }
    </style>

    <div class="container review-container py-4">
        <h2 class="review-header">Chi tiết đánh giá sản phẩm</h2>

        <div class="card review-card p-4">
            <h4 class="review-title">{{ $review->title ?? 'Không có tiêu đề' }}</h4>

            <div class="review-row">
                <div class="label-text">Biến thể sản phẩm:</div>
                <div class="value-text">{{ $review->productVariant->sku ?? 'N/A' }}</div>
            </div>

            <div class="review-row">
                <div class="label-text">Tên sản phẩm:</div>
                <div class="value-text">{{ $review->productVariant->product->name ?? 'N/A' }}</div>
            </div>

            <div class="review-row">
                <div class="label-text">Người dùng:</div>
                <div class="value-text">{{ $review->user->name ?? 'N/A' }}</div>
            </div>

            <div class="review-row">
                <div class="label-text">Số sao đánh giá:</div>
                <div class="value-text">
                    @for ($i = 1; $i <= 5; $i++)
                        <span style="color: {{ $i <= $review->rating ? 'orange' : 'lightgray' }}">★</span>
                    @endfor
                </div>
            </div>

            <div class="review-row">
                <div class="label-text">Nội dung đánh giá:</div>
                <div class="value-text">{{ $review->content ?? 'N/A' }}</div>
            </div>

            <div class="review-row">
                <div class="label-text">Ngày tạo:</div>
                <div class="value-text">{{ optional($review->created_at)->format('d/m/Y H:i') }}</div>
            </div>

            <div class="review-row">
                <div class="label-text">Trạng thái:</div>
                <div class="value-text">
                    @if ($review->status == 'approved')
                        <span class="badge bg-success">Đã duyệt</span>
                    @elseif ($review->status == 'pending')
                        <span class="badge bg-warning text-dark">Chờ duyệt</span>
                    @elseif ($review->status == 'rejected')
                        <span class="badge bg-danger">Từ chối</span>
                    @else
                        <span class="badge bg-secondary">Không rõ</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="btn-group-custom">
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-custom btn-custom-outline">← Quay lại</a>

            <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST"
                onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này không?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-custom btn-danger">Xóa</button>
            </form>
        </div>
    </div>
@endsection
