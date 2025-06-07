@extends('layouts.admin')
@section('title', 'Chi tiết đánh giá sản phẩm')
@section('content')
    <div class="container mx-auto px-4 py-10">
        <div class="bg-white shadow-xl rounded-2xl p-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">Chi tiết đánh giá sản phẩm</h1>

            <div class="space-y-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">Thông tin đánh giá</h2>

                    <p><strong>Biến thể sản phẩm:</strong> {{ $review->productVariant->sku ?? 'N/A' }}</p>

                    <p><strong>Tên sản phẩm:</strong> {{ $review->productVariant->product->name ?? 'N/A' }}</p>

                    <p><strong>Người dùng:</strong> {{ $review->user->name ?? 'N/A' }}</p>

                    <p><strong>Đánh giá:</strong>
    @for ($i = 1; $i <= 5; $i++)
        <span style="color: {{ $i <= $review->rating ? 'orange' : 'lightgray' }}">★</span>
    @endfor
</p>

                    <p><strong>Tiêu đề:</strong> {{ $review->title ?? 'N/A' }}</p>

                    <p><strong>Nội dung:</strong> {{ $review->content ?? 'N/A' }}</p>

                    <p><strong>Ngày tạo:</strong> {{ optional($review->created_at)->format('d/m/Y H:i') ?? 'N/A' }}</p>

                    <p><strong>Trạng thái:</strong>
                        @if ($review->status == 'approved')
                            <span class="text-green-600 font-semibold">Đã duyệt</span>
                        @elseif ($review->status == 'pending')
                            <span class="text-yellow-600 font-semibold">Chờ duyệt</span>
                        @elseif ($review->status == 'rejected')
                            <span class="text-red-600 font-semibold">Từ chối</span>
                        @else
                            <span class="text-gray-600 font-semibold">Không rõ</span>
                        @endif
                    </p>
                </div>

                <div class="flex space-x-2">
                    {{-- <a href="{{ route('admin.reviews.edit', $review->id) }}"
                        class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-md text-sm font-medium shadow transition duration-300">
                        Sửa
                    </a> --}}

                    <form action="{{ route('admin.reviews.destroy', $review->id) }}" method="POST"
                        onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này không?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-1 bg-red-500 hover:bg-red-600 px-4 py-2 rounded-md text-sm font-medium shadow transition duration-300">
                            Xóa
                        </button>
                    </form>

                    <a href="{{ route('admin.reviews.index') }}"
                        class="inline-flex items-center gap-1 bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-md text-sm font-medium shadow transition duration-300">
                        Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
