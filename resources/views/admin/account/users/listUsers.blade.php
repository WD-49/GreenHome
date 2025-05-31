@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        {{-- Tiêu đề trang và các nút hành động chính --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Quản lý tài khoản Người dùng</h1>
            <div>
                <a href="{{ route('admin.account.createUser') }}" class="btn btn-success shadow-sm">
                    <i class="fas fa-plus-circle fa-sm text-white-50"></i> Thêm Người dùng mới
                </a>
                <a href="{{ route('admin.account.trashedUsers') }}" class="btn btn-warning shadow-sm">
                    <i class="fas fa-trash-restore fa-sm text-white-50"></i> Thùng rác
                </a>
            </div>
        </div>

        {{-- Thông báo thành công --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Form lọc và tìm kiếm thông tin --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-filter"></i> Bộ lọc và Tìm kiếm Người dùng
                </h6>
            </div>
            <div class="card-body">
                {{-- 
                Sử dụng class="row g-2" (Bootstrap 5) hoặc "form-row" (Bootstrap 4)
                "g-2" tạo khoảng cách nhỏ giữa các cột.
                "align-items-end" để căn các input xuống dưới nếu chúng có chiều cao khác nhau (ví dụ khi có validation message).
            --}}
                <form method="GET" action="{{ route('admin.account.listUsers') }}">
                    <div class="row g-2 align-items-end"> {{-- Cho Bootstrap 5. Nếu dùng Bootstrap 4, thay bằng "form-row align-items-end" --}}
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            {{-- <label for="name" class="sr-only">Tên</label> --}}
                            <input type="text" name="name" id="name" class="form-control form-control-sm"
                                placeholder="Tên người dùng" value="{{ request('name') }}">
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            {{-- <label for="email" class="sr-only">Email</label> --}}
                            <input type="text" name="email" id="email" class="form-control form-control-sm"
                                placeholder="Email" value="{{ request('email') }}">
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            {{-- <label for="phone" class="sr-only">SĐT</label> --}}
                            <input type="text" name="phone" id="phone" class="form-control form-control-sm"
                                placeholder="Số điện thoại" value="{{ request('phone') }}">
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            {{-- <label for="address" class="sr-only">Địa chỉ</label> --}}
                            <input type="text" name="address" id="address" class="form-control form-control-sm"
                                placeholder="Địa chỉ" value="{{ request('address') }}">
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6 mb-2">
                            {{-- <label for="gender" class="sr-only">Giới tính</label> --}}
                            <select name="gender" id="gender" class="form-control form-control-sm">
                                <option value="">Giới tính</option>
                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-12 mb-2 text-md-right text-sm-left">
                            <button type="submit" class="btn btn-primary btn-sm mr-1"> {{-- mr-1 (Bootstrap 4) hoặc me-1 (Bootstrap 5) --}}
                                <i class="fas fa-search"></i> Lọc
                            </button>
                            <a href="{{ route('admin.account.listUsers') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-sync-alt"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bảng danh sách người dùng --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-users"></i> Danh sách Người dùng
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTableUsers" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center align-middle" style="width: 5%;">STT</th>
                                <th class="align-middle" style="width: 20%;">Tên</th>
                                <th class="align-middle" style="width: 25%;">Email</th>
                                <th class="text-center align-middle" style="width: 15%;">Vai trò</th>
                                <th class="text-center align-middle" style="width: 15%;">Trạng thái</th>
                                <th class="text-center align-middle" style="width: 20%;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $key => $user)
                                <tr>
                                    <td class="text-center align-middle">{{ $users->firstItem() + $key }}</td>
                                    <td class="align-middle">{{ $user->name }}</td>
                                    <td class="align-middle">{{ $user->email }}</td>
                                    <td class="text-center align-middle">
                                        {{-- Giả sử người dùng thông thường có role 'user' --}}
                                        @if ($user->role == 'client')
                                            <span>User</span>
                                        @else
                                            <span>{{ ucfirst($user->role) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($user->status == 1)
                                            <span class="text-success"
                                                style="font-size: 0.85em; padding: 0.5em 0.75em;">Hoạt động</span>
                                        @else
                                            <span class="text-danger"
                                                style="font-size: 0.85em; padding: 0.5em 0.75em;">Ngừng hoạt động</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group" role="group" aria-label="User Actions">
                                            <a href="{{ route('admin.account.detailAccUser', $user->id) }}"
                                                class="btn btn-info btn-sm" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.account.editUser', $user->id) }}"
                                                class="btn btn-warning btn-sm" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.account.softDeleteUser', $user->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa tạm thời người dùng này không?')">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    title="Xóa tạm thời">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.account.resetPassUser', $user->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Bạn có chắc muốn đặt lại mật khẩu cho người dùng này không?')">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary btn-sm"
                                                    title="Reset mật khẩu">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Không tìm thấy tài khoản người dùng nào phù
                                        hợp.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang --}}
                @if ($users->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{-- Đảm bảo sử dụng pagination::bootstrap-5 nếu bạn dùng Bootstrap 5 --}}
                        {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
