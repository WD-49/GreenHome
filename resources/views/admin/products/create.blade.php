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
        {{-- Chọn loại sản phẩm --}}
        <div class="mb-4">
            <label class="form-label fw-bold">Loại sản phẩm</label>
            {{-- Ẩn: default là sản phẩm đơn --}}
            {{-- <input type="hidden" name="is_variant" value="0"> --}}
            <div class="form-check">
                <input class="form-check-input" type="radio" name="is_variant" id="simple_product" value="0"
                    {{ old('is_variant') ? '' : 'checked' }}>
                <label class="form-check-label" for="simple_product">Sản phẩm đơn (không có thuộc tính)</label>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="radio" name="is_variant" id="variant_product" value="1"
                    {{ old('is_variant') ? 'checked' : '' }}>

                <label class="form-check-label" for="variant_product">Sản phẩm có biến thể</label>
            </div>
        </div>
        {{-- Thuộc tính (chỉ hiện nếu chọn sản phẩm biến thể) --}}
        <div id="attribute-section" class="mb-4 d-none">
            <label class="form-label fw-bold">Chọn thuộc tính và giá trị</label>

            <div class="mb-3">
                <select id="attributeSelector" class="form-select">
                    <option value="">-- Chọn thuộc tính --</option>
                    @foreach ($attributes as $attribute)
                        <option value="{{ $attribute->id }}">{{ $attribute->name }}</option>
                    @endforeach
                </select>
            </div>

            <div id="attributeValuesContainer">
                {{-- Các checkbox sẽ render ở đây khi chọn thuộc tính --}}
            </div>
            {{-- điền giá và số lượng default --}}
            <div id="defaultPriceQuantity" class="mb-3 d-none">
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="defaultPrice" class="col-form-label">Giá mặc định</label>
                    </div>
                    <div class="col-auto">
                        <input type="number" id="defaultPrice" class="form-control" min="0" step="1000"
                            value="0">
                    </div>
                    <div class="col-auto">
                        <label for="defaultQuantity" class="col-form-label">Số lượng mặc định</label>
                    </div>
                    <div class="col-auto">
                        <input type="number" id="defaultQuantity" class="form-control" min="0" value="0">
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-outline-primary mt-3" id="generateVariants">
                ⚡ Tạo nhanh các biến thể từ tổ hợp giá trị
            </button>
        </div>

        {{-- Danh sách biến thể --}}
        <div id="variant-table" class="mt-4 d-none">
            <h5 class="mb-3">Danh sách biến thể</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Biến thể</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>ảnh</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="variantBody">
                    {{-- Các dòng biến thể được thêm động bằng JS --}}
                </tbody>
            </table>
        </div>

        {{-- Chỉ hiện với sản phẩm đơn --}}
        <div id="simple-fields" class="mb-4">
            <div class="mb-3">
                <label for="simple_price" class="form-label">Giá sản phẩm <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('simple_price') is-invalid @enderror"
                    name="simple_price" id="simple_price" min="0" step="1000"
                    value="{{ old('simple_price') }}">
                @error('simple_price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="simple_quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('simple_quantity') is-invalid @enderror"
                    name="simple_quantity" id="simple_quantity" min="0" value="{{ old('simple_quantity') }}">
                @error('simple_quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- <div class="mb-3">
                <label for="simple_image" class="form-label">Hình ảnh sản phẩm <span class="text-danger">*</span></label>
                <input type="file" class="form-control @error('simple_image') is-invalid @enderror"
                    name="simple_image" id="simple_image">
                @error('simple_image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div> --}}
        </div>

        {{-- Nút Thêm sản phẩm --}}
        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success" id="submitProductBtn">
                ✅ Thêm sản phẩm
            </button>
        </div>

    </form>


@endsection

@push('scripts')
    {{-- CKEditor --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>


    <!-- Script JS xử lý động -->
    <!-- Đảm bảo tải jQuery & Bootstrap JS (với Collapse) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bạn có thể dùng CDN Bootstrap JS cho collapse -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let editorInstance;

        document.addEventListener('DOMContentLoaded', function() {
            const simpleRadio = document.getElementById('simple_product');
            const variantRadio = document.getElementById('variant_product');
            const attributeSection = document.getElementById('attribute-section');
            const variantTable = document.getElementById('variant-table');
            const simpleFields = document.getElementById('simple-fields');
            const defaultPriceQuantity = document.getElementById('defaultPriceQuantity');
            const attributeSelector = document.getElementById('attributeSelector');
            const attributeValuesContainer = document.getElementById('attributeValuesContainer');
            const variantBody = document.getElementById('variantBody');

            const attributeData = @json($attributes->mapWithKeys(fn($a) => [$a->id => $a->attributeValues->map(fn($v) => ['id' => $v->id, 'value' => $v->value])]));

            function toggleVariantMode() {
                const isVariant = variantRadio.checked;

                // Ẩn/hiện theo loại sản phẩm
                attributeSection.classList.toggle('d-none', !isVariant);
                variantTable.classList.toggle('d-none', !isVariant);
                defaultPriceQuantity.classList.toggle('d-none', !isVariant);
                simpleFields.classList.toggle('d-none', isVariant);

                // Vô hiệu hóa các input trong simple-fields khi chọn biến thể
                const simpleInputs = simpleFields.querySelectorAll('input');
                simpleInputs.forEach(input => {
                    input.disabled = isVariant;
                });
            }

            // Gọi ngay để áp dụng theo giá trị đã chọn
            toggleVariantMode();

            simpleRadio.addEventListener('change', toggleVariantMode);
            variantRadio.addEventListener('change', toggleVariantMode);

            // Khi chọn thuộc tính
            attributeSelector.addEventListener('change', function() {
                const attributeId = this.value;
                if (!attributeId || attributeValuesContainer.querySelector(`[data-attr="${attributeId}"]`))
                    return;

                const values = attributeData[attributeId];
                if (!values) return;

                const wrapper = document.createElement('div');
                wrapper.classList.add('mb-2');
                wrapper.setAttribute('data-attr', attributeId);

                let html =
                    `<label class="form-label">Thuộc tính: ${this.options[this.selectedIndex].text}</label>
    <div>`;
                values.forEach(v => {
                    html += `
        <div class="form-check form-check-inline">
            <input class="form-check-input attribute-checkbox" type="checkbox" name="attributes[${attributeId}][]"
                value="${v.id}" id="attr_${attributeId}_${v.id}">
            <label class="form-check-label" for="attr_${attributeId}_${v.id}">${v.value}</label>
        </div>`;
                });
                html += `
    </div>`;
                wrapper.innerHTML = html;
                attributeValuesContainer.appendChild(wrapper);
            });

            // Tạo biến thể
            document.getElementById('generateVariants').addEventListener('click', function() {
                variantBody.innerHTML = '';

                const defaultPrice = Number(document.getElementById('defaultPrice').value) || 0;
                const defaultQuantity = Number(document.getElementById('defaultQuantity').value) || 0;
                const checkedBoxes = document.querySelectorAll('.attribute-checkbox:checked');

                const attrMap = {};
                checkedBoxes.forEach(cb => {
                    const attrId = cb.name.match(/attributes\[(\d+)]/)[1];
                    if (!attrMap[attrId]) attrMap[attrId] = [];
                    attrMap[attrId].push({
                        id: cb.value,
                        label: document.querySelector(`label[for="${cb.id}"]`).textContent
                            .trim()
                    });
                });

                const attrValuesArray = Object.values(attrMap);
                if (attrValuesArray.length === 0) return;

                let combinations = [
                    []
                ];
                attrValuesArray.forEach(group => {
                    const newCombos = [];
                    combinations.forEach(prefix => {
                        group.forEach(item => {
                            newCombos.push([...prefix, item]);
                        });
                    });
                    combinations = newCombos;
                });

                combinations.forEach((combo, index) => {
                    const variantName = combo.map(c => c.label).join(' / ');
                    const variantIdValues = combo.map(c => c.id).join(',');

                    variantBody.insertAdjacentHTML('beforeend', `
    <tr>
        <td>
            ${variantName}
            <input type="hidden" name="variants[${index}][values]" value="${variantIdValues}">
        </td>
        <td><input type="number" name="variants[${index}][price]" class="form-control" required
                value="${defaultPrice}"></td>
        <td><input type="number" name="variants[${index}][quantity]" class="form-control" required
                value="${defaultQuantity}"></td>
        <td><input type="file" name="variants[${index}][image]" class="form-control"></td>
        <td><button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()">❌</button></td>
    </tr>
    `);
                });
            });

            // xử lý mô tả bằng CKEditor
            ClassicEditor
                .create(document.querySelector('#description'))
                .then(editor => {
                    editorInstance = editor;
                });

            document.getElementById('submitProductBtn').addEventListener('click', function() {
                document.querySelector('#description').value = editorInstance.getData();
            });
        });
    </script>
@endpush
