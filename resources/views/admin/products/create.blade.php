@extends('layouts.admin')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <h1 class="text-center">{{ $title }}</h1>

    <!-- Hiển thị lỗi của validation -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Hiển thị lỗi do xảy ra exception trong quá trình xử lý lưu -->
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            id="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" name="category_id"
                            id="category_id" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="brand_id" class="form-label">Thương hiệu <span class="text-danger">*</span></label>
                        <select class="form-select @error('brand_id') is-invalid @enderror" name="brand_id" id="brand_id"
                            required>
                            <option value="">-- Chọn thương hiệu --</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                            <option value="" {{ old('status') === null ? 'selected' : '' }}>-- Chọn trạng thái --
                            </option>
                            <option value="1" {{ old('status') === '1' ? 'selected' : '' }}>Đang bán</option>
                            <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Dừng bán</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="date_of_entry" class="form-label">Ngày nhập</label>
                        <input type="date" class="form-control @error('date_of_entry') is-invalid @enderror"
                            name="date_of_entry" id="date_of_entry"
                            value="{{ old('date_of_entry', now()->format('Y-m-d')) }}">
                        @error('date_of_entry')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Hình ảnh sản phẩm</label>
                        <input type="file" class="form-control @error('image') is-invalid @enderror" name="image"
                            id="image">
                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description"
                    rows="4">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <hr>

        <!-- Phần chọn tổ hợp thuộc tính -->
        <div id="attribute-selection">
            <h4>Chọn các thuộc tính để thêm vào biến thể</h4>
            @foreach ($attributes as $attribute)
                <div class="form-check">
                    <input class="form-check-input variant-attribute-checkbox" type="checkbox"
                        id="attribute_{{ $attribute->id }}" value="{{ $attribute->id }}"
                        data-name="{{ $attribute->name }}">
                    <label class="form-check-label" for="attribute_{{ $attribute->id }}">
                        {{ $attribute->name }}
                    </label>
                </div>
            @endforeach
        </div>

        <!-- Phần tạo biến thể -->
        <div id="variants-block" class="mt-4">
            <h4>Biến thể</h4>
            <div id="variants-container">
                <!-- Các biến thể được thêm động vào đây -->
            </div>
            <button type="button" class="btn btn-secondary mt-2" id="add-variant">
                + Thêm biến thể
            </button>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>

    <!-- Script JS xử lý động -->
    <!-- Đảm bảo tải jQuery & Bootstrap JS (với Collapse) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bạn có thể dùng CDN Bootstrap JS cho collapse -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Biến toàn cục để lưu các thuộc tính được chọn và dữ liệu gốc của attributes.
        let selectedAttributes = [];
        // Chuyển dữ liệu attributes từ server sang JS (đảm bảo cấu trúc phù hợp như đã mô tả).
        let attributesData = @json($attributes);
        // console.log(attributesData);

        $(document).ready(function() {
            // Khi checkbox thuộc tính được tích hoặc bỏ tích,
            // cập nhật danh sách các thuộc tính được chọn.
            $('.variant-attribute-checkbox').change(function() {
                updateSelectedAttributes();
                // Khi thay đổi thuộc tính đã chọn, ta xóa hết các biến thể đã tạo,
                // tránh sự không đồng bộ giữa cấu trúc biến thể và các thuộc tính hiện hành.
                $('#variants-container').empty();
            });

            function updateSelectedAttributes() {
                selectedAttributes = [];
                $('.variant-attribute-checkbox:checked').each(function() {
                    let attrId = $(this).val();
                    let attrData = attributesData.find(item => item.id == attrId);
                    if (attrData) {
                        selectedAttributes.push(attrData);
                    }
                });
            }

            // Thêm biến thể mới dựa trên danh sách thuộc tính được chọn.
            $('#add-variant').click(function() {
                // Nếu chưa chọn thuộc tính thì thông báo cho người dùng.
                if (selectedAttributes.length === 0) {
                    alert("Vui lòng chọn ít nhất 1 thuộc tính ở trên!");
                    return;
                }

                // Đếm số biến thể hiện tại để tạo chỉ số duy nhất cho name input.
                let variantIndex = $('#variants-container .variant-row').length;
                let variantHtml = '<div class="variant-row border p-3 mb-2" data-index="' + variantIndex +
                    '">';
                variantHtml += '<div class="row">';

                // Với mỗi thuộc tính được chọn, tạo dropdown lựa chọn giá trị.
                selectedAttributes.forEach(function(attribute) {
                    console.log('Attribute:',
                        attribute.attribute_values); // Kiểm tra cấu trúc của đối tượng attribute

                    variantHtml += '<div class="col-md-3">';
                    variantHtml += '<label class="form-label">' + attribute.name + '</label>';
                    variantHtml += '<select class="form-select" name="variants[' + variantIndex +
                        '][attributes][' + attribute.id + ']">';
                    variantHtml += '<option value="">-- Chọn ' + attribute.name + ' --</option>';

                    if (attribute.attribute_values && attribute.attribute_values.length > 0) {
                        attribute.attribute_values.forEach(function(value) {
                            console.log('Value:',
                                value); // Kiểm tra cấu trúc của đối tượng value
                            variantHtml += '<option value="' + value.id + '">' + value
                                .value + '</option>';
                        });
                    }
                    variantHtml += '</select>';
                    variantHtml += '</div>';
                });


                variantHtml += '</div>'; // end row

                // Phần nhập thông tin bổ sung của biến thể: giá, số lượng, ảnh
                // Sử dụng collapse của Bootstrap để ẩn/hiện thông tin khi cần.
                variantHtml +=
                    '<button type="button" class="btn btn-secondary btn-sm mt-2" data-bs-toggle="collapse" data-bs-target="#variantInfo_' +
                    variantIndex + '">';
                variantHtml += 'Nhập thông tin bổ sung';
                variantHtml += '</button>';
                variantHtml += '<div class="collapse mt-2" id="variantInfo_' + variantIndex + '">';
                variantHtml += '<div class="card card-body">';
                variantHtml += '<div class="mb-3">';
                variantHtml += '<label class="form-label">Giá</label>';
                variantHtml += '<input type="number" class="form-control" name="variants[' + variantIndex +
                    '][price]">';
                variantHtml += '</div>';
                variantHtml += '<div class="mb-3">';
                variantHtml += '<label class="form-label">Số lượng</label>';
                variantHtml += '<input type="number" class="form-control" name="variants[' + variantIndex +
                    '][quantity]">';
                variantHtml += '</div>';
                variantHtml += '<div class="mb-3">';
                variantHtml += '<label class="form-label">Ảnh</label>';
                variantHtml += '<input type="file" class="form-control" name="variants[' + variantIndex +
                    '][image]">';
                variantHtml += '</div>';
                variantHtml += '</div>';
                variantHtml += '</div>';

                variantHtml += '</div>'; // end variant-row

                // Thêm biến thể mới vào container.
                $('#variants-container').append(variantHtml);
            });
        });
    </script>
@endsection
