@extends('layouts.admin')

@section('content')
    <h2 class="text-center mb-4">Chỉnh sửa thông tin website</h2>

    <form action="{{ route('admin.web_info.update') }}" method="POST">
        @csrf
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light fw-bold">Thông tin cấu hình</div>
            <div class="card-body row g-4">

                <div class="col-md-6">
                    <label class="form-label">Tên website</label>
                    <input type="text" name="web_name" value="{{ $webInfos['web_name'] ?? '' }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ $webInfos['email'] ?? '' }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ $webInfos['phone'] ?? '' }}" class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" value="{{ $webInfos['address'] ?? '' }}" class="form-control">
                </div>
            <div class="mb-3">
                <label for="sortDes" class="form-label">Mô tả</label>
                <textarea class="form-control" id="sortDes" name="sortDes" rows="3">{{ old('sortDes', $webInfos['sortDes'] ?? '') }}</textarea>
            </div>

            </div>
        </div>

        <div class="text-end">
            <a href="{{ route('admin.web_info.show') }}" class="btn btn-secondary me-2">
                <i class="fa fa-arrow-left me-1"></i> Quay lại
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save me-1"></i> Lưu thay đổi
            </button>
        </div>
    </form>
      <!-- Thêm CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#sortDes'))
            .catch(error => {
                console.error(error);
            });

        document.getElementById('name').addEventListener('input', function() {
            let name = this.value;

            // Hàm để chuyển đổi từ có dấu sang không dấu
            let slug = name.toLowerCase()
                .normalize("NFD") // Tách các ký tự dấu ra khỏi ký tự
                .replace(/[\u0300-\u036f]/g, "") // Loại bỏ các ký tự dấu
                .replace(/\s+/g, '-') // Thay thế khoảng trắng thành dấu gạch nối
                .replace(/[^a-z0-9\-]/g, '') // Loại bỏ các ký tự đặc biệt
                .replace(/^-+|-+$/g, ''); // Xóa dấu gạch nối ở đầu và cuối

            document.getElementById('slug').value = slug; // Đặt slug vào ô input
        });
    </script>
@endsection
