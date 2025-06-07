@extends('layouts.admin')
@section('title', 'Quản lý đánh giá sản phẩm')
@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow-xl rounded-2xl p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6"> Quản lý đánh giá sản phẩm</h1>
        <div class="mb-4">
            <a href="{{ route('admin.reviews.trash') }}"
               class="inline-block bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-lg shadow">
               xem thùng rác
            </a>
        </div>
        
       
     



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
                        <th class="px-6 py-4 font-semibold text-center">Thay đổi trạng thái</th> <!-- Cột mới -->
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
                                {{-- <a href="{{ route('admin.reviews.edit', $review->id) }}"
                                   class="inline-block bg-blue-500 hover:bg-blue-600 px-3 py-1 rounded-lg text-xs font-medium shadow">
                                    Sửa
                                </a> --}}
                                <a href="{{ route('admin.reviews.show', $review->id) }}" class="ml-2">Xem</a>
                                <form action="{{ route('admin.reviews.destroy', $review->id) }}"
                                      method="POST" class="inline-block ml-2"
                                      onsubmit="return confirm('Bạn có chắc muốn xóa đánh giá này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded-lg text-xs font-medium shadow">
                                         Xóa
                                    </button>
                                </form>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if(in_array($review->status, ['pending', 'rejected']))
                                    <form action="{{ route('admin.reviews.updateStatus', $review->id) }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn duyệt đánh giá này không?');" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit"
                                            class="bg-green-500 hover:bg-green-600 px-3 py-1 rounded-lg text-xs font-medium shadow">
                                            Duyệt
                                        </button>
                                    </form>
                                @elseif($review->status === 'approved')
                                    <form action="{{ route('admin.reviews.updateStatus', $review->id) }}" method="POST"
                                        onsubmit="return confirm('Bạn có chắc muốn ẩn đánh giá này không?');" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit"
                                            class="bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded-lg text-xs font-medium shadow">
                                            Ẩn
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @if($reviews->isEmpty())
                        <tr>
                            <td colspan="9" class="text-center text-gray-500 py-6">Không có đánh giá nào.</td>
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
