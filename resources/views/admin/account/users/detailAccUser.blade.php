@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h2>Chi tiết tài khoản</h2>

        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                Thông tin người dùng
            </div>
            <div class="card-body">
                <p><strong>Tên:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Role:</strong> {{ $user->role }}</p>
                <p><strong>Trạng thái:</strong> {{ $user->status == 1 ? 'Hoạt động' : 'Ngừng hoạt động' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                Thông tin hồ sơ
            </div>

            @if ($user->profile)
                <div class="card-body">
                    <p><strong>Số điện thoại:</strong> {{ $user->profile->phone }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $user->profile->address }}</p>
                    <p><strong>Giới tính:</strong> {{ $user->profile->gender == 'male' ? 'Nam' : 'Nữ' }}</p>

                    @if ($user->profile->user_image)
                        <p><strong>Ảnh đại diện:</strong></p>
                        <img src="{{ asset($user->profile->user_image) }}" alt="Ảnh đại diện" class="img-thumbnail"
                            style="max-width: 200px;">
                    @endif
                </div>
            @else
                <div class="card-body text-danger">
                    <p>Không có thông tin hồ sơ người dùng.</p>
                </div>
            @endif
        </div>

        <a href="{{ route('admin.account.listUsers') }}" class="btn btn-secondary mt-3">Quay lại danh sách</a>
    </div>
@endsection
