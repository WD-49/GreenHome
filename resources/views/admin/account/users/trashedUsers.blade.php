@extends('layouts.admin')
@section('title', 'Quản lý tài khoản đã xóa tạm thời')
@section('content')
    <!-- Start Content-->
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Quản lý tài khoản đã xóa tạm thời</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <h6 class="breadcrumb-item active">Home / Tài khoản / Quản lý tài khoản người dùng / Quản lý tài khoản đã
                        xóa tạm thời</h6>
                </ol>
            </div>
        </div>
        {{-- Thông báo thành công --}}
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <!-- Datatables  -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Thùng rác người dùng</h5>
                        <div>
                            <a href="{{ route('admin.account.listUsers') }}" class="btn btn-light shadow-sm">
                                <i class="fas fa-arrow-left"></i> Quay lại danh sách người dùng
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
                                @foreach ($trashedUsers as $key => $user)
                                    <tr>
                                        <td>{{ $trashedUsers->firstItem() + $key }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role }}</td>
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
                                            <form action="{{ route('admin.account.restoreUser', $user->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                <button onclick="return confirm('Khôi phục người dùng này?');"
                                                    type="submit" class="btn btn-success btn-sm">Khôi phục</button>
                                            </form>

                                            <form action="{{ route('admin.account.forceDeleteUser', $user->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button onclick="return confirm('Xóa vĩnh viễn người dùng này?');"
                                                    type="submit" class="btn btn-danger btn-sm">Xóa vĩnh viễn</button>
                                            </form>
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
