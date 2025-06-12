@extends('layouts.admin')
@section('title', 'Thùng rác đánh giá sản phẩm')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="container mx-auto px-4 py-10">
    <div class="bg-white shadow-md rounded-xl p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800"> Thùng rác đánh giá sản phẩm</h1>
    <a href="{{ route('admin.reviews.index') }}"
   class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-100 to-blue-200 hover:from-blue-200 hover:to-blue-300 transition-all px-4 py-2 rounded-xl shadow-md text-sm font-semibold text-blue-800">
    <i class="bi bi-arrow-left-circle text-blue-700"></i>
    Quay lại trang đánh giá
</a>

<br> <br>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-sm text-left text-gray-700 bg-white">
                <thead class="bg-gray-100 text-gray-800 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-3 border">#</th>
                        <th class="px-4 py-3 border">Biến thể sản phẩm</th>
                        <th class="px-4 py-3 border">Người dùng</th>
                        <th class="px-4 py-3 border">Đánh giá</th>
                        <th class="px-4 py-3 border">Tiêu đề</th>
                        <th class="px-4 py-3 border">Ngày tạo</th>
                        <th class="px-4 py-3 border">Trạng thái</th>
                        <th class="px-4 py-3 border text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reviews as $review)
                        <tr class="border-t hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-3 border">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 border">{{ $review->productVariant->sku ?? 'N/A' }}</td>
                            <td class="px-4 py-3 border">{{ $review->user->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 border text-yellow-500">{!! str_repeat('★', $review->rating) !!}</td>
                            <td class="px-4 py-3 border">{{ $review->title ?? '(Không có)' }}</td>
                            <td class="px-4 py-3 border">
                                {{ $review->created_at?->format('d/m/Y H:i') ?? 'Không rõ' }}
                            </td>
                            <td class="px-4 py-3 border">
                                @switch($review->status)
                                    @case('approved')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Đã duyệt</span>
                                        @break
                                    @case('pending')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Chờ duyệt</span>
                                        @break
                                    @case('rejected')
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Từ chối</span>
                                        @break
                                    @default
                                        <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Không rõ</span>
                                @endswitch
                            </td>
                           <td class="px-4 py-3 border text-center whitespace-nowrap">
    <div class="flex justify-center gap-2">
        <!-- Nút Phục hồi giống nút lịch sử -->
        <form action="{{ route('admin.reviews.restore', $review->id) }}" method="POST"
              onsubmit="return confirm('Bạn có chắc muốn phục hồi đánh giá này không?');">
            @csrf
            <button class="inline-flex items-center gap-1 border border-green-500 text-green-600 hover:bg-green-50 transition px-3 py-1.5 rounded-lg shadow-sm text-sm font-medium">
                <i class="bi bi-arrow-clockwise text-green-500"></i> Phục hồi
            </button>
        </form>

        <!-- Nút Xóa vĩnh viễn -->
        <form action="{{ route('admin.reviews.forceDelete', $review->id) }}" method="POST"
              onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn đánh giá này không?');">
            @csrf
            @method('DELETE')
            <button class="inline-flex items-center gap-1 border border-red-500 text-red-600 hover:bg-red-50 transition px-3 py-1.5 rounded-lg shadow-sm text-sm font-medium">
                <i class="bi bi-x-circle text-red-500"></i> Xóa
            </button>
        </form>
    </div>
</td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-gray-500 py-6">Thùng rác hiện đang trống.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $reviews->links('pagination::tailwind') }}
        </div>
    </div>
</div>

@endsection
