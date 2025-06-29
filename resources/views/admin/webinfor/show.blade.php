@extends('layouts.admin')

@section('content')
    <h2 class="text-center mb-4">Thông tin website</h2>

    {{-- Card: Thông tin cấu hình --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light fw-bold">
            <i class="fa fa-cog me-1"></i> Cấu hình chung
        </div>
        <div class="card-body">
            <div class="row g-4">

                {{-- Tên website --}}
                <div class="col-md-6 col-lg-4">
                    <label class="text-muted small">Tên website</label>
                    <div class="fw-semibold fs-6 text-dark">{{ $webInfos['web_name'] ?? 'Chưa có' }}</div>
                </div>

                {{-- Email --}}
                <div class="col-md-6 col-lg-4">
                    <label class="text-muted small">Email liên hệ</label>
                    <div class="text-dark">{{ $webInfos['email'] ?? 'Chưa có' }}</div>
                </div>

                {{-- Số điện thoại --}}
                <div class="col-md-6 col-lg-4">
                    <label class="text-muted small">Số điện thoại</label>
                    <div class="text-dark">{{ $webInfos['phone'] ?? 'Chưa có' }}</div>
                </div>

                {{-- Địa chỉ --}}
                <div class="col-md-6 col-lg-4">
                    <label class="text-muted small">Địa chỉ</label>
                    <div class="text-dark">{{ $webInfos['address'] ?? 'Chưa có' }}</div>
                </div>

                {{-- Mô tả ngắn --}}
                <div class="col-6">
                    <label class="text-muted small">Mô tả ngắn</label>
                    <div class="border rounded bg-light p-1">
                        {!! $webInfos['sortDes'] ?? '<em>Chưa có mô tả</em>' !!}
                    </div>
                </div>
            </div>

            {{-- Nút chỉnh sửa --}}
            <div class="text-end mt-4">
                <a href="{{ route('admin.web_info.edit') }}" class="btn btn-warning">
                    <i class="fa fa-edit me-1"></i> Chỉnh sửa
                </a>
            </div>
        </div>
    </div>

    {{-- Nút quay lại --}}
    <div class="mt-3">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left me-1"></i> Quay lại trang quản trị
        </a>
    </div>
@endsection
