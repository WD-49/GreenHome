@extends('layouts.admin')
@section('title', 'Quản lý tài khoản người dùng')
@section('content')
    <!-- Start Content-->
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Quản lý tài khoản người dùng</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <h6 class="breadcrumb-item active">Home / Tài khoản / Quản lý tài khoản người dùng</h6>
                </ol>
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
        <!-- Datatables  -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách người dùng</h5>
                        <div>
                            <a href="{{ route('admin.account.trashedUsers') }}" class="btn btn-danger shadow-sm">
                                <i class="fas fa-trash-restore fa-sm text-white-50"></i> Thùng rác
                            </a>    
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="datatable" class="table table-bordered dt-responsive table-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên</th>
                                    <th>Email</th>
                                    <th>Vai Trò</th>
                                    <th>Trạng Thái</th>
                                    <th>Hành Động</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $item => $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $item }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{-- Giả sử người dùng thông thường có role 'user' --}}
                                            @if ($user->role == 'client')
                                                <span>User</span>
                                            @else
                                                <span>{{ ucfirst($user->role) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($user->status == 1)
                                                <span class="text-success"
                                                    style="font-size: 0.85em; padding: 0.5em 0.75em;">Hoạt động</span>
                                            @else
                                                <span class="text-danger"
                                                    style="font-size: 0.85em; padding: 0.5em 0.75em;">Ngừng hoạt động</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group" aria-label="User Actions">
                                                <a href="{{ route('admin.account.detailAccUser', $user->id) }}"
                                                    class="btn btn-info btn-sm" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div> <!-- container-fluid -->

@endsection
