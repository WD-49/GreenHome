@extends('layouts.admin')
@section('title', 'Quản lý tài khoản người dùng')
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Datatables css -->
    <link href="../../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="../../assets/libs/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="../../assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="../../assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />

    <!-- App css -->
    <link href="../../assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons -->
    <link href="../../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
@endpush
@section('content')

    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <!-- Start Content-->
    <div class="container-xxxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Quản lý tài khoản quản trị</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <h6 class="breadcrumb-item active">Home / Tài khoản / Quản lý tài khoản quản trị</h6>
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
                        <h5 class="card-title mb-0">Danh sách quản trị</h5>
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
                                @foreach ($admins as $item => $user)
                                    <tr>
                                        <td>{{ $item + 1 }}</td>
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

                                                <button type="button"
                                                    class="btn btn-{{ $user->role == 'client' ? 'warning' : 'primary' }} btn-sm toggle-role-btn"
                                                    data-user-id="{{ $user->id }}"
                                                    data-current-role="{{ $user->role }}"
                                                    title="{{ $user->role == 'client' ? 'Chuyển thành Admin' : 'Chuyển thành Client' }}">
                                                    <i class="fas fa-user-shield"></i> {{-- Icon cho phân quyền --}}
                                                </button>

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

@push('scripts')
    <!-- Vendor -->
    <script src="../../assets/libs/jquery/jquery.min.js"></script>
    <script src="../../assets/libs/simplebar/simplebar.min.js"></script>
    <script src="../../assets/libs/node-waves/waves.min.js"></script>
    <script src="../../assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="../../assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
    <script src="../../assets/libs/feather-icons/feather.min.js"></script>

    <!-- Datatables js -->
    <script src="../../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>

    <!-- dataTables.bootstrap5 -->
    <script src="../../assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>

    <!-- buttons.colVis -->
    <script src="../../assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>

    <!-- buttons.bootstrap5 -->
    <script src="../../assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>

    <!-- dataTables.keyTable -->
    <script src="../../assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="../../assets/libs/datatables.net-keytable-bs5/js/keyTable.bootstrap5.min.js"></script>

    <!-- dataTable.responsive -->
    <script src="../../assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>

    <!-- dataTables.select -->
    <script src="../../assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
    <script src="../../assets/libs/datatables.net-select-bs5/js/select.bootstrap5.min.js"></script>

    <!-- Datatable Demo App Js -->
    <script src="../../assets/js/pages/datatable.init.js"></script>


    <script>
        $(document).ready(function() {
            // Lấy CSRF token từ meta tag và cấu hình jQuery để tự động gửi
            const csrfTokenGlobal = $('meta[name="csrf-token"]').attr('content');
            if (csrfTokenGlobal) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfTokenGlobal
                    }
                });
            } else {
                console.error("CSRF token meta tag not found. AJAX requests might fail.");
            }

            // Hàm hiển thị thông báo tạm thời
            function showTemporaryMessage(message, type = 'success', duration = 3000) {
                let alertClass = 'alert-success';
                if (type === 'error') alertClass = 'alert-danger';
                if (type === 'info') alertClass = 'alert-info';
                if (type === 'warning') alertClass = 'alert-warning';

                const messageId = 'temp-alert-' + Date.now();
                const messageDiv = $(
                    `<div class="alert ${alertClass} alert-dismissible fade show m-2" role="alert" id="${messageId}" style="position:fixed; top: 20px; right: 20px; z-index: 1050; min-width: 250px; max-width: 400px;">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`
                );
                $('body').prepend(messageDiv);
                setTimeout(function() {
                    $('#' + messageId).fadeOut(500, function() {
                        $(this).remove();
                    });
                }, duration);
            }

            // Khởi tạo DataTables
            if (!$.fn.DataTable.isDataTable('#datatable')) {
                $('#datatable').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    // "language": {
                    //     "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
                    // }
                });
            }

            // Xử lý nút "Phân quyền" (Toggle Role)
            $('#datatable').on('click', '.toggle-role-btn', function() {
                const button = $(this);
                const userId = button.data('userId');
                const currentRole = button.data('currentRole');
                const newRole = currentRole === 'client' ? 'admin' : 'client';
                const confirmText = `Bạn có chắc muốn chuyển người dùng này thành ${newRole} không?`;

                if (!confirm(confirmText)) {
                    return;
                }

                const originalHtml = button.html();
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                const updateRoleUrl = `{{ route('admin.account.toggleUserRole', ['user' => ':userId']) }}`
                    .replace(':userId', userId);

                $.ajax({
                    url: updateRoleUrl,
                    type: 'POST',
                    data: {
                        new_role: newRole
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            const roleCell = $(`#user-role-${userId}`);
                            const newRoleBadge = newRole === 'client' ?
                                '<span class="badge bg-info text-dark">Client</span>' :
                                '<span class="badge bg-success">Admin</span>';
                            roleCell.html(newRoleBadge);

                            button.data('currentRole', newRole);
                            button.attr('title', newRole === 'client' ? 'Chuyển thành Admin' :
                                'Chuyển thành Client');
                            button.removeClass('btn-warning btn-primary').addClass(newRole ===
                                'client' ? 'btn-warning' : 'btn-primary');
                            button.tooltip('dispose').tooltip(); // Cập nhật tooltip

                            showTemporaryMessage(response.message, 'success');

                            // Tải lại trang sau một khoảng thời gian ngắn
                            setTimeout(function() {
                                location.reload();
                            }, 500);
                        } else {
                            showTemporaryMessage(response.message ||
                                'Thay đổi vai trò thất bại.', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error toggling role:', xhr.responseText);
                        const errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr
                            .responseJSON.message : 'Lỗi hệ thống khi thay đổi vai trò.';
                        showTemporaryMessage(errorMessage, 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // Xử lý form "Xóa tạm thời" (Soft Delete User)
            $('#datatable').on('submit', 'form.soft-delete-user-form', function(e) {
                e.preventDefault();
                const form = $(this);
                const button = form.find('button[type="submit"]');
                const originalButtonHtml = button.html();

                if (!confirm('Bạn có chắc chắn muốn xóa tạm thời người dùng này không?')) {
                    return;
                }

                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showTemporaryMessage(response.message, 'info');
                            form.closest('tr').fadeOut(500, function() {
                                $(this).remove();
                                if ($.fn.DataTable.isDataTable('#datatable')) {
                                    $('#datatable').DataTable().row(this).remove()
                                        .draw(); // 'this' trỏ đến row đã fadeOut
                                }
                            });
                        } else {
                            showTemporaryMessage(response.message || 'Xóa thất bại.', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error soft deleting user:', xhr.responseText);
                        const errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr
                            .responseJSON.message : 'Lỗi hệ thống khi xóa tạm thời.';
                        showTemporaryMessage(errorMessage, 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html(originalButtonHtml);
                    }
                });
            });

            // Xử lý form "Reset mật khẩu" (Reset Password)
            $('#datatable').on('submit', 'form.reset-pass-user-form', function(e) {
                e.preventDefault();
                const form = $(this);
                const button = form.find('button[type="submit"]');
                const originalButtonHtml = button.html();

                if (!confirm('Bạn có chắc muốn đặt lại mật khẩu cho người dùng này không?')) {
                    return;
                }

                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showTemporaryMessage(response.message, 'success');
                        } else {
                            showTemporaryMessage(response.message ||
                                'Đặt lại mật khẩu thất bại.', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error resetting password:', xhr.responseText);
                        const errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr
                            .responseJSON.message : 'Lỗi hệ thống khi đặt lại mật khẩu.';
                        showTemporaryMessage(errorMessage, 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html(originalButtonHtml);
                    }
                });
            });

            // Khởi tạo tooltip cho tất cả các phần tử có data-bs-toggle="tooltip" trên trang
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
