@extends('layouts.admin')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <h1 class="text-center">Sửa Biến Thể Cho Sản Phẩm: {{ $product->name }}</h1>
    <div class="container mt-5">
        <form action="{{ route('admin.products.variants.update', [$product, $productVariant]) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            {{-- @dd($productVariant) --}}

            <div class="row">
                <!-- Cột trái -->
                <div class="col-md-6">

                    <div class="mb-3">
                        <label for="price" class="form-label">Giá</label>
                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                            id="price" name="price" value="{{ old('price', $productVariant->price) }}">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Số Lượng</label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity"
                            name="quantity" value="{{ old('quantity', $productVariant->quantity) }}">
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Hình ảnh</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                            id="image">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if ($productVariant->image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $productVariant->image) }}" alt="Ảnh biến thể"
                                    width="150">
                            </div>
                        @endif
                    </div>


                </div>

                <!-- Cột phải -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng Thái</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                            <option value="1" {{ old('status', $productVariant->status) == '1' ? 'selected' : '' }}>
                                Đang bán
                            </option>
                            <option value="0" {{ old('status', $productVariant->status) == '0' ? 'selected' : '' }}>
                                Dừng bán
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="dropdown" style="width: 550px;">
                        <label class="form-label">Chọn Thuộc Tính</label>
                        <button class="form-select dropdown-toggle w-100" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Chọn thuộc tính
                        </button>

                        <div class="dropdown-menu p-3 w-100" style="max-height: 400px; overflow-y: auto;">
                            @foreach ($attributes as $attribute)
                                @php
                                    $pvvGroup = $productVariant->productVariantValues->groupBy(
                                        fn($item) => $item->attributeValue->attribute_id,
                                    );

                                    $checked = old('attributes')
                                        ? in_array($attribute->id, old('attributes'))
                                        : $pvvGroup->has($attribute->id);

                                    $selectedValue =
                                        old('attribute_values.' . $attribute->id) ??
                                        ($pvvGroup->get($attribute->id)[0]->attribute_value_id ?? '');
                                @endphp

                                <div class="form-check mb-3 w-100">
                                    <input class="form-check-input attribute-checkbox" type="checkbox" name="attributes[]"
                                        id="attribute_{{ $attribute->id }}" value="{{ $attribute->id }}"
                                        {{ $checked ? 'checked' : '' }}>
                                    <label class="form-check-label" for="attribute_{{ $attribute->id }}">
                                        {{ $attribute->name }}
                                    </label>

                                    <select
                                        class="form-select mt-2 w-100 attribute-select @error('attribute_values.' . $attribute->id) is-invalid @enderror"
                                        name="attribute_values[{{ $attribute->id }}]" {{ $checked ? '' : 'disabled' }}>
                                        <option value="">Chọn giá trị cho {{ $attribute->name }}</option>
                                        @foreach ($attribute->attributeValues ?? [] as $value)
                                            <option value="{{ $value->id }}"
                                                {{ $selectedValue == $value->id ? 'selected' : '' }}>
                                                {{ $value->value }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('attribute_values.' . $attribute->id)
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach

                            @error('attributes')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <button type="submit" class="btn btn-primary">Cập Nhật Biến Thể</button>
        </form>
    </div>

    {{-- <script>
        document.querySelectorAll('.attribute-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const select = this.closest('.form-check').querySelector('.attribute-select');
                if (select) {
                    select.disabled = !this.checked;
                }
            });
        });
    </script> --}}
@endsection
