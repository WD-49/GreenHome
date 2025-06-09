@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        {{-- Tiêu đề trang --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Quản lý tài khoản Admin</h1>
            <div>
                {{-- <a href="{{ route('admin.account.createAdmin') }}" class="btn btn-success shadow-sm">
                    <i class="fas fa-plus-circle fa-sm text-white-50"></i> Thêm Admin mới
                </a> --}}
                <a href="{{ route('admin.account.trashedAdmins') }}" class="btn btn-warning shadow-sm">
                    <i class="fas fa-trash-restore fa-sm text-white-50"></i> Thùng rác
                </a>
            </div>
        </div>

        {{-- Form tìm kiếm thông tin --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-search"></i> Bộ lọc và Tìm kiếm
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.account.listAdmins') }}">
                    {{-- 
                Sử dụng "row g-2" (Bootstrap 5) để có khoảng cách nhỏ giữa các cột.
                Nếu bạn dùng Bootstrap 4, hãy thay "row g-2" bằng "form-row".
                "align-items-center" hoặc "align-items-end" để căn chỉnh các phần tử theo chiều dọc.
            --}}
                    <div class="row g-2 align-items-center">
                        <div class="col-lg col-md-6 col-sm-12 mb-2 mb-lg-0">
                            <input type="text" name="name" class="form-control form-control-sm"
                                placeholder="Tên admin" value="{{ request('name') }}">
                        </div>
                        <div class="col-lg col-md-6 col-sm-12 mb-2 mb-lg-0">
                            <input type="text" name="email" class="form-control form-control-sm" placeholder="Email"
                                value="{{ request('email') }}">
                        </div>
                        <div class="col-lg col-md-6 col-sm-12 mb-2 mb-lg-0">
                            <input type="text" name="phone" class="form-control form-control-sm" placeholder="SĐT"
                                value="{{ request('phone') }}">
                        </div>
                        <div class="col-lg col-md-6 col-sm-12 mb-2 mb-lg-0">
                            <input type="text" name="address" class="form-control form-control-sm" placeholder="Địa chỉ"
                                value="{{ request('address') }}">
                        </div>
                        <div class="col-lg col-md-6 col-sm-12 mb-2 mb-lg-0">
                            <select name="gender" class="form-control form-control-sm">
                                <option value="">Giới tính</option>
                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                            </select>
                        </div>
                        <div class="col-lg-auto col-md-12 text-md-right mt-2 mt-lg-0">
                            {{-- 
                        Sử dụng "me-1" (Bootstrap 5) hoặc "mr-1" (Bootstrap 4) để tạo khoảng cách phải cho nút Lọc.
                    --}}
                            <button type="submit" class="btn btn-primary btn-sm me-1">
                                <i class="fas fa-filter"></i> Lọc
                            </button>
                            <a href="{{ route('admin.account.listAdmins') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-sync-alt"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        {{-- Bảng danh sách --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-list-ul"></i> Danh sách Tài khoản Admin
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTableAdmins" width="100%" cellspacing="0">
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
                            @forelse ($admins as $key => $admin)
                                <tr>
                                    <td class="text-center align-middle">{{ $admins->firstItem() + $key }}</td>
                                    <td class="align-middle">{{ $admin->name }}</td>
                                    <td class="align-middle">{{ $admin->email }}</td>
                                    <td class="text-center align-middle">
                                        @if ($admin->role == 'admin')
                                            <span>Admin</span>
                                        @else
                                            <span>{{ ucfirst($admin->role) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if ($admin->status == 1)
                                            <span class="text-success">Hoạt động</span>
                                        @else
                                            <span class="text-danger">Ngừng hoạt động</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group" role="group" aria-label="Admin Actions">
                                            <a href="{{ route('admin.account.detailAccAdmin', $admin->id) }}"
                                                class="btn btn-info btn-sm" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.account.editAdmin', $admin->id) }}"
                                                class="btn btn-warning btn-sm" title="Chỉnh sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.account.softDeleteAdmin', $admin->id) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa tạm thời admin này không?')">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm" title="Xóa tạm thời">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.account.resetPassAdmin', $admin->id) }}"
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
                                    <td colspan="6" class="text-center">Không tìm thấy tài khoản admin nào phù hợp.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang --}}
                @if ($admins->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $admins->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
