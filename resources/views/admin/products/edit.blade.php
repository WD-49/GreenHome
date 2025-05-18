@extends('layouts.admin')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <h1 class="text-center">{{ $title }}</h1>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Cột trái --}}
            <div class="col-md-6">
                {{-- Tên sản phẩm --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                        id="name" value="{{ old('name', $product->name) }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Danh mục --}}
                <div class="mb-3">
                    <label for="category_id" class="form-label">Danh mục</label>
                    <select class="form-select @error('category_id') is-invalid @enderror" name="category_id"
                        id="category_id">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Thương hiệu --}}
                <div class="mb-3">
                    <label for="brand_id" class="form-label">Thương hiệu</label>
                    <select class="form-select @error('brand_id') is-invalid @enderror" name="brand_id" id="brand_id">
                        <option value="">-- Chọn thương hiệu --</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}"
                                {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('brand_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Số lượng --}}
                <div class="mb-3">
                    <label for="quantity" class="form-label">Số lượng</label>
                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" name="quantity"
                        id="quantity" value="{{ old('quantity', $product->quantity) }}">
                    @error('quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Trạng thái --}}
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select class="form-select @error('status') is-invalid @enderror" name="status">
                        <option value="" {{ old('status', $product->status) === null ? 'selected' : '' }}>
                            Chọn trạng thái
                        </option>
                        <option value="1" {{ old('status', $product->status) == 1 ? 'selected' : '' }}>
                            Đang bán
                        </option>
                        <option value="0" {{ old('status', $product->status) == 0 ? 'selected' : '' }}>
                            Dừng bán
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Cột phải --}}
            <div class="col-md-6">
                {{-- Giá gốc --}}
                <div class="mb-3">
                    <label for="price" class="form-label">Giá gốc</label>
                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                        name="price" id="price" value="{{ old('price', $product->price) }}">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Giá khuyến mãi --}}
                <div class="mb-3">
                    <label for="promotional_price" class="form-label">Giá khuyến mãi</label>
                    <input type="number" step="0.01"
                        class="form-control @error('promotional_price') is-invalid @enderror" name="promotional_price"
                        id="promotional_price" value="{{ old('promotional_price', $product->promotional_price) }}">
                    @error('promotional_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ngày nhập --}}
                <div class="mb-3">
                    <label for="date_of_entry" class="form-label">Ngày nhập</label>
                    <input type="date" class="form-control @error('date_of_entry') is-invalid @enderror"
                        name="date_of_entry" id="date_of_entry"
                        value="{{ old('date_of_entry', \Carbon\Carbon::parse($product->date_of_entry)->format('Y-m-d')) }}">
                    @error('date_of_entry')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Hình ảnh --}}
                <div class="mb-3">
                    <label for="image" class="form-label">Hình ảnh</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                        id="image">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if (!empty($product->image))
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $product->image) }}" width="100" alt="Ảnh sản phẩm">
                        </div>
                    @endif
                </div>

                {{-- Mô tả --}}
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description"
                        rows="3">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Nút submit --}}
        <div class="text-center">
            <button type="submit" class="btn btn-success">Cập nhật sản phẩm</button>
        </div>
    </form>
@endsection
