@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Thêm Thương hiệu</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.brands.store') }}">
            @csrf

            <div id="brand-rows">
                @php $index = 0; @endphp
                <div class="row brand-row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label">Tên thương hiệu</label>
                        <input type="text" class="form-control" name="brands[{{ $index }}][name]" value="{{ old("brands.$index.name") }}">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Mô tả</label>
                        <textarea class="form-control ckeditor" name="brands[{{ $index }}][description]">{{ old("brands.$index.description") }}</textarea>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-remove-row" title="Xóa dòng này">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" id="btn-add-row" class="btn btn-info mb-3">
                <i class="fa-solid fa-plus"></i> Thêm thương hiệu
            </button>

            <div>
                <button type="submit" class="btn btn-success">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu tất cả
                </button>
                <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>

    {{-- CKEditor --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
    <script>
        let index = 1; // index cho dòng mới
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

        // Thêm dòng mới
        document.getElementById('btn-add-row').addEventListener('click', () => {
            const brandRows = document.getElementById('brand-rows');
            const row = document.createElement('div');
            row.className = 'row brand-row mb-3 g-3';
            row.innerHTML = `
                <div class="col-md-6">
                    <label class="form-label">Tên thương hiệu</label>
                    <input type="text" class="form-control" name="brands[${index}][name]">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Mô tả</label>
                    <textarea class="form-control ckeditor" name="brands[${index}][description]"></textarea>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-remove-row" title="Xóa dòng này">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            `;
            brandRows.appendChild(row);
            index++;
            initCKEditors();
        });

        // Xóa dòng
        document.addEventListener('click', function (e) {
            if (e.target.closest('.btn-remove-row')) {
                const row = e.target.closest('.brand-row');
                if (row) row.remove();
            }
        });

        // Khởi tạo ban đầu
        window.addEventListener('DOMContentLoaded', initCKEditors);
    </script>
@endsection
