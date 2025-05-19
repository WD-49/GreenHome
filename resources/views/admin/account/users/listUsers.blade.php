@extends('layouts.admin')

@section('content')
    <h1>Quản lý tài khoản người dùng</h1>
    <h3>Tìm kiếm thông tin người dùng</h3>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form lọc --}}
    <form method="GET" action="{{ route('admin.account.listUsers') }}" class="mb-4">
        <div class="row">
            <div class="col"><input type="text" name="name" class="form-control" placeholder="Tên"
                    value="{{ request('name') }}"></div>
            <div class="col"><input type="text" name="email" class="form-control" placeholder="Email"
                    value="{{ request('email') }}"></div>
            <div class="col"><input type="text" name="phone" class="form-control" placeholder="SĐT"
                    value="{{ request('phone') }}"></div>
            <div class="col"><input type="text" name="address" class="form-control" placeholder="Địa chỉ"
                    value="{{ request('address') }}"></div>
            <div class="col">
                <select name="gender" class="form-control">
                    <option value="">Giới tính</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                </select>
            </div>
            <div class="col"><button type="submit" class="btn btn-primary">Lọc</button></div>
        </div>
    </form>

    <a href="{{ route('admin.account.createUser') }}" class="btn btn-primary mb-3">Thêm người dùng</a>
    <a href="{{ route('admin.account.trashedUsers') }}" class="btn btn-secondary mb-3">Thùng rác</a>

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
            @forelse ($users as $key => $user)
                <tr>
                    <td>{{ $users->firstItem() + $key }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role }}</td>
                    <td>{{ $user->status == 1 ? 'Hoạt động' : 'Ngừng hoạt động' }}</td>
                    <td>
                        <a href="{{ route('admin.account.detailAccUser', $user->id) }}" class="btn btn-info">Xem chi
                            tiết</a>
                        <a href="{{ route('admin.account.editUser', $user->id) }}" class="btn btn-warning btn-sm">Sửa</a>

                        <form action="{{ route('admin.account.softDeleteUser', $user->id) }}" method="POST"
                            style="display:inline-block;">
                            @csrf
                            <button onclick="return confirm('Bạn có chắc muốn xóa người dùng này?');" type="submit"
                                class="btn btn-danger btn-sm">Xóa</button>
                        </form>
                        <form action="{{ route('admin.account.resetPassword', $user->id) }}" method="POST"
                            class="d-inline"
                            onsubmit="return confirm('Bạn có chắc muốn đặt lại mật khẩu người dùng này không?')">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">Reset mật khẩu</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Không tìm thấy người dùng nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
@endsection
