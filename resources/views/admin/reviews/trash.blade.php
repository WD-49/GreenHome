@extends('layouts.admin')
@section('title', 'Thùng rác đánh giá sản phẩm')
@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-xl rounded-2xl p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"> Thùng rác đánh giá sản phẩm</h1>

        <a href="{{ route('admin.reviews.index') }}" class="inline-block mb-4 bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg shadow">
            Quay lại danh sách đánh giá
        </a>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="min-w-full bg-white text-sm text-left text-gray-700">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-4 font-semibold">#</th>
                        <th class="px-6 py-4 font-semibold">Biến thể sản phẩm</th>
                        <th class="px-6 py-4 font-semibold">Người dùng</th>
                        <th class="px-6 py-4 font-semibold">Đánh giá</th>
                        <th class="px-6 py-4 font-semibold">Tiêu đề</th>
                        <th class="px-6 py-4 font-semibold">Ngày tạo</th>
                        <th class="px-6 py-4 font-semibold">Trạng thái</th>
                        <th class="px-6 py-4 font-semibold text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reviews as $review)
                        <tr class="hover:bg-gray-50 border-b transition duration-200">
                            <td class="px-6 py-4">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">{{ $review->productVariant->sku ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $review->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-yellow-500">
                                {{ str_repeat('★', $review->rating) }}
                            </td>
                            <td class="px-6 py-4">{{ $review->title }}</td>
                            <td class="px-6 py-4">{{ $review->created_at }}</td>
                            <td class="px-6 py-4">
                                @if ($review->status == 'approved')
                                    <span class="text-green-600 font-semibold">Đã duyệt</span>
                                @elseif ($review->status == 'pending')
                                    <span class="text-yellow-600 font-semibold">Chờ duyệt</span>
                                @elseif ($review->status == 'rejected')
                                    <span class="text-red-600 font-semibold">Từ chối</span>
                                @else
                                    <span class="text-gray-600 font-semibold">Không rõ</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.reviews.restore', $review->id) }}" method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn phục hồi đánh giá này không?');" class="inline-block">
                                    @csrf
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 px-3 py-1 rounded-lg text-xs font-medium shadow text-white">
                                        Phục hồi
                                    </button>
                                </form>

                                <form action="{{ route('admin.reviews.forceDelete', $review->id) }}" method="POST"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa vĩnh viễn đánh giá này không?');" class="inline-block ml-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded-lg text-xs font-medium shadow text-white">
                                        Xóa vĩnh viễn
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if($reviews->isEmpty())
                        <tr>
                            <td colspan="8" class="text-center text-gray-500 py-6">Thùng rác đang trống.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $reviews->links('pagination::tailwind') }}
        </div>
    </div>
</div>

@endsection
