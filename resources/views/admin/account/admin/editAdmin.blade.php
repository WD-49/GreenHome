@extends('layouts.admin')

@section('content')
<h2>Chỉnh sửa tài khoản người dùng</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.account.updateAdmin', $admins->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label>Tên</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $admins->name) }}">
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $admins->email) }}">
    </div>

    <div class="form-group">
        <label>Vai trò</label>
        <select name="role" class="form-control">
            <option value="user" {{ (old('role', $admins->role) == 'user') ? 'selected' : '' }}>Người dùng</option>
            <option value="admin" {{ (old('role', $admins->role) == 'admin') ? 'selected' : '' }}>Quản trị viên</option>
        </select>
    </div>

    <div class="form-group">
        <label>Trạng thái</label>
        <select name="status" class="form-control">
            <option value="1" {{ (old('status', $admins->status) == 1) ? 'selected' : '' }}>Hoạt động</option>
            <option value="0" {{ (old('status', $admins->status) == 0) ? 'selected' : '' }}>Ngừng hoạt động</option>
        </select>
    </div>

    <div class="form-group">
        <label>Số điện thoại</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $admins->profile->phone ?? '') }}">
    </div>

    <div class="form-group">
        <label>Địa chỉ</label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $admins->profile->address ?? '') }}">
    </div>

    <div class="form-group">
        <label>Giới tính</label>
        <select name="gender" class="form-control">
            <option value="">-- Chọn giới tính --</option>
            <option value="male" {{ (old('gender', $admins->profile->gender ?? '') == 'male') ? 'selected' : '' }}>Nam</option>
            <option value="female" {{ (old('gender', $admins->profile->gender ?? '') == 'female') ? 'selected' : '' }}>Nữ</option>
        </select>
    </div>

    <div class="form-group">
        <label>Ảnh đại diện</label><br>
        @if (!empty($admins->profile->user_image))
            <img src="{{ asset($admins->profile->user_image) }}" alt="Ảnh đại diện" class="img-thumbnail" style="max-width: 200px;">
        @endif
        <input type="file" name="user_image" class="form-control-file mt-2">
    </div>

    <button type="submit" class="btn btn-primary mt-3">Cập nhật</button>
</form>
@endsection
