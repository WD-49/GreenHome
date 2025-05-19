@extends('layouts.admin')

@section('content')
    <h1>Quản lý tài khoản admin</h1>
    {{-- <a href="{{ route('admin.account.createAdmin') }}" class="btn btn-primary mb-3">Thêm admin</a> --}}
    <h3>Tìm kiếm thông tin</h3>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    {{-- Form tìm kiếm thông tin --}}
    <form method="GET" action="{{ route('admin.account.listAdmins') }}" class="mb-4">
        <div class="row">
            <div class="col">
                <input type="text" name="name" class="form-control" placeholder="Tên" value="{{ request('name') }}">
            </div>
            <div class="col">
                <input type="text" name="email" class="form-control" placeholder="Email"
                    value="{{ request('email') }}">
            </div>
            <div class="col">
                <input type="text" name="phone" class="form-control" placeholder="SĐT" value="{{ request('phone') }}">
            </div>
            <div class="col">
                <input type="text" name="address" class="form-control" placeholder="Địa chỉ"
                    value="{{ request('address') }}">
            </div>
            <div class="col">
                <select name="gender" class="form-control">
                    <option value="">Giới tính</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary">Lọc</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </div>
    </form>
    <a href="{{ route('admin.account.createAdmin') }}" class="btn btn-primary mb-3">Thêm người dùng</a>
    <a href="{{ route('admin.account.trashedAdmins') }}" class="btn btn-secondary mb-3">Thùng rác</a>
    {{-- Bảng danh sách --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Role</th>
                <th>Trạng thái</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($admins as $key => $admin)
                <tr>
                    <td>{{ $admins->firstItem() + $key }}</td>
                    <td>{{ $admin->name }}</td>
                    <td>{{ $admin->email }}</td>
                    <td>{{ $admin->role }}</td>
                    <td>{{ $admin->status == 1 ? 'Hoạt động' : 'Ngừng hoạt động' }}</td>
                    <td>
                        <a href="{{ route('admin.account.detailAccAdmin', $admin->id) }}" class="btn btn-info">Xem</a>
                        <a href="{{ route('admin.account.editAdmin', $admin->id) }}" class="btn btn-warning">Sửa</a>
                        <form action="{{ route('admin.account.softDeleteAdmin', $admin->id) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa admin này không?')">Xóa</button>
                        </form>
                        <form action="{{ route('admin.account.resetPassAdmin', $admin->id) }}" method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Bạn có chắc muốn đặt lại mật khẩu người dùng này không?')">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">Reset mật khẩu</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Không tìm thấy admin nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center">
        {{ $admins->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
@endsection
