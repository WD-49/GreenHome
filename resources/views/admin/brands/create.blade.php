@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Thêm Thương hiệu</h1>

        <form method="POST" action="{{ route('admin.brands.store') }}">
            @csrf

            <div id="brand-rows">
                @php
                    $oldBrands = old('brands', [['name' => '', 'description' => '']]);
                @endphp

                @foreach ($oldBrands as $index => $brand)
                    <div class="row brand-row mb-3 g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên thương hiệu</label>
                            <input type="text"
                                   name="brands[{{ $index }}][name]"
                                   value="{{ $brand['name'] }}"
                                   class="form-control @error('brands.' . $index . '.name') is-invalid @enderror">
                            @error('brands.' . $index . '.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label class="form-label">Mô tả</label>
                            <textarea name="brands[{{ $index }}][description]"
                                      class="form-control ckeditor @error('brands.' . $index . '.description') is-invalid @enderror"
                            >{{ $brand['description'] }}</textarea>
                            @error('brands.' . $index . '.description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-remove-row" title="Xóa dòng này">
                                <i class="">Xóa</i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

           

            <div>
                 <button type="button" id="btn-add-row" class="btn btn-info ">
                <i class=""></i> Tạo thêm một thương hiệu
            </button>
                <button type="submit" class="btn btn-success">
                    <i class=""></i> Lưu tất cả
                </button>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                    <i class=""></i> Quay lại
                </a>
            </div>
        </form>
    </div>

    {{-- CKEditor --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
    <script>
        let index = {{ count($oldBrands) }};
        const editors = [];

        function initCKEditors() {
            document.querySelectorAll('textarea.ckeditor').forEach((textarea) => {
                if (!textarea.classList.contains('ckeditor-initialized')) {
                    ClassicEditor
                        .create(textarea)
                        .then(editor => {
                            editors.push(editor);
                            textarea.classList.add('ckeditor-initialized');
                        })
                        .catch(error => console.error(error));
                }
            });
        }

        document.getElementById('btn-add-row').addEventListener('click', () => {
            const brandRows = document.getElementById('brand-rows');
            const row = document.createElement('div');
            row.className = 'row brand-row mb-3 g-3';
            row.innerHTML = `
                <div class="col-md-6">
                    <label class="form-label">Tên thương hiệu</label>
                    <input type="text" name="brands[${index}][name]" class="form-control">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Mô tả</label>
                    <textarea name="brands[${index}][description]" class="form-control ckeditor"></textarea>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-remove-row" title="Xóa dòng này">
                        <i class="">Xóa</i>
                    </button>
                </div>
            `;
            brandRows.appendChild(row);
            index++;
            initCKEditors();
        });

        document.addEventListener('click', function (e) {
            if (e.target.closest('.btn-remove-row')) {
                const row = e.target.closest('.brand-row');
                if (row) row.remove();
            }
        });

        window.addEventListener('DOMContentLoaded', initCKEditors);
    </script>
@endsection
