@extends('layouts.admin')
@section('title', 'Tạo đánh giá sản phẩm')
@section('content')
<div class="container mx-auto px-4 py-10">
    <div class="bg-white shadow-xl rounded-2xl p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Tạo đánh giá sản phẩm</h1>

        <form action="{{ route('admin.reviews.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="product_variant_id" class="block text-sm font-medium text-gray-700">Biến thể sản phẩm</label>
                <select name="product_variant_id" id="product_variant_id"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach ($productVariants as $variant)
                        <option value="{{ $variant->id }}">{{ $variant->sku }}</option>
                    @endforeach
                </select>
            </div>

           

            <div>
                <label for="rating" class="block text-sm font-medium text-gray-700">Đánh giá</label>
                <input type="number" name="rating" id="rating" min="1" max="5"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Tiêu đề</label>
                <input type="text" name="title" id="title"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
            </div>

            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Nội dung</label>
                <textarea name="content" id="content" rows="4"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required></textarea>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Trạng thái</label>
                <select name="status" id="status"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="pending">Chờ duyệt</option>
                    <option value="approved">Đã duyệt</option>
                    <option value="rejected">Từ chối</option>
                </select>
            </div>
            <div class="flex justify-end">
                <button type="submit"
                    class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 px-4 py-2 rounded-md text-sm font-medium text-white shadow transition duration-300">
                    Tạo đánh giá
                </button>
                <a href="{{ route('admin.reviews.index') }}"
                    class="ml-2 inline-flex items-center gap-2 bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-md text-sm font-medium text-gray-800 shadow transition duration-300">
                    Quay lại
                </a>
            </div>
        </form>
    </div>
</div>              
@endsection



