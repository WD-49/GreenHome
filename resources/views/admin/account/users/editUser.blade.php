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

<form action="{{ route('admin.account.updateUser', $user->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
        <label>Tên</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
    </div>

    <div class="form-group">
        <label>Vai trò</label>
        <select name="role" class="form-control">
            <option value="client" {{ (old('role', $user->role) == 'client') ? 'selected' : '' }}>Khách hàng</option>
            <option value="admin" {{ (old('role', $user->role) == 'admin') ? 'selected' : '' }}>Quản trị viên</option>
        </select>
    </div>

    <div class="form-group">
        <label>Trạng thái</label>
        <select name="status" class="form-control">
            <option value="1" {{ (old('status', $user->status) == 1) ? 'selected' : '' }}>Hoạt động</option>
            <option value="0" {{ (old('status', $user->status) == 0) ? 'selected' : '' }}>Ngừng hoạt động</option>
        </select>
    </div>

    <div class="form-group">
        <label>Số điện thoại</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->profile->phone ?? '') }}">
    </div>

    <div class="form-group">
        <label>Địa chỉ</label>
        <input type="text" name="address" class="form-control" value="{{ old('address', $user->profile->address ?? '') }}">
    </div>

    <div class="form-group">
        <label>Giới tính</label>
        <select name="gender" class="form-control">
            <option value="">-- Chọn giới tính --</option>
            <option value="nam" {{ (old('gender', $user->profile->gender ?? '') == 'nam') ? 'selected' : '' }}>Nam</option>
            <option value="nu" {{ (old('gender', $user->profile->gender ?? '') == 'nu') ? 'selected' : '' }}>Nữ</option>
            <option value="khac" {{ (old('gender', $user->profile->gender ?? '') == 'khac') ? 'selected' : '' }}>Khác</option>
        </select>
    </div>

    <div class="form-group">
        <label>Ảnh đại diện</label><br>
        @if (!empty($profile->user_image))
            <img src="{{ asset($profile->user_image) }}" alt="Ảnh đại diện" class="img-thumbnail" style="max-width: 200px;">
        @endif
        <input type="file" name="user_image" class="form-control-file mt-2">
    </div>

    <button type="submit" class="btn btn-primary mt-3">Cập nhật</button>
</form>
@endsection
