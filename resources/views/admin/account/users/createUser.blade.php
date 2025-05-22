@extends('layouts.admin')

@section('content')
    <h2>Tạo tài khoản người dùng</h2>

    {{-- Hiển thị lỗi tổng --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.account.storeUser') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Tên --}}
        <div class="form-group">
            <label>Tên</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Mật khẩu --}}
        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="password" class="form-control">
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Xác nhận mật khẩu --}}
        <div class="form-group">
            <label>Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        {{-- Vai trò --}}
        <div class="form-group">
            <label>Vai trò</label>
            <select name="role" class="form-control">
                <option value="client" {{ old('role') == 'client' ? 'selected' : '' }}>Người dùng</option>
                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Quản trị viên</option>
            </select>
            @error('role')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Trạng thái --}}
        <div class="form-group">
            <label>Trạng thái</label>
            <select name="status" class="form-control">
                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Hoạt động</option>
                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Ngừng hoạt động</option>
            </select>
            @error('status')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Số điện thoại --}}
        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            @error('phone')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Địa chỉ --}}
        <div class="form-group">
            <label>Địa chỉ</label>
            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
            @error('address')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Giới tính --}}
        <div class="form-group">
            <label>Giới tính</label>
            <select name="gender" class="form-control">
                <option value="">-- Chọn giới tính --</option>
                <option value="nam" {{ old('gender') == 'nam' ? 'selected' : '' }}>Nam</option>
                <option value="nu" {{ old('gender') == 'nu' ? 'selected' : '' }}>Nữ</option>
                <option value="khac" {{ old('gender') == 'khac' ? 'selected' : '' }}>Khác</option>
            </select>
            @error('gender')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Ảnh đại diện --}}
        <div class="form-group">
            <label>Ảnh đại diện</label>
            <input type="file" name="user_image" class="form-control-file">
            @error('user_image')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-success mt-3">Tạo người dùng</button>
    </form>
@endsection
