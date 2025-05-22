@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h2>Chi tiết tài khoản</h2>

        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                Thông tin người dùng
            </div>
            <div class="card-body">
                <p><strong>Tên:</strong> {{ $admins->name }}</p>
                <p><strong>Email:</strong> {{ $admins->email }}</p>
                <p><strong>Role:</strong> {{ $admins->role }}</p>
                <p><strong>Trạng thái:</strong> {{ $admins->status == 1 ? 'Hoạt động' : 'Ngừng hoạt động' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                Thông tin hồ sơ
            </div>

            @if ($admins->profile)
                <div class="card-body">
                    <p><strong>Số điện thoại:</strong> {{ $admins->profile->phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $admins->profile->address }}</p>
                    <p><strong>Giới tính:</strong> {{ $admins->profile->gender == 'male' ? 'Nam' : 'Nữ' }}</p>

                    @if ($admins->profile->user_image)
                        <p><strong>Ảnh đại diện:</strong></p>
                        <img src="{{ asset('storage/' . $admins->profile->user_image) }}" alt="Ảnh đại diện" class="img-thumbnail"
                            style="max-width: 200px;">
                            
                    @endif
                </div>
            @else
                <div class="card-body text-danger">
                    <p>Không có thông tin hồ sơ người dùng.</p>
                </div>
            @endif
        </div>

        <a href="{{ route('admin.account.listAdmins') }}" class="btn btn-secondary mt-3">Quay lại danh sách</a>
    </div>
@endsection
