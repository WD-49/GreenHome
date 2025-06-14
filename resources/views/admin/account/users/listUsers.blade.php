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

                                                {{-- NÚT PHÂN QUYỀN --}}
                                                {{-- Điều kiện để nút phân quyền hiển thị (ví dụ: không cho phép phân quyền chính mình nếu bạn là admin) --}}

                                                <button type="button"
                                                    class="btn btn-{{ $user->role == 'client' ? 'warning' : 'primary' }} btn-sm toggle-role-btn"
                                                    data-user-id="{{ $user->id }}"
                                                    data-current-role="{{ $user->role }}"
                                                    title="{{ $user->role == 'client' ? 'Chuyển thành Admin' : 'Chuyển thành Client' }}">
                                                    <i class="fas fa-user-shield"></i> {{-- Icon cho phân quyền --}}
                                                </button>


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

@push('scripts')
    <!-- Vendor -->
    <script src="../../assets/libs/jquery/jquery.min.js"></script>
    <script src="../../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
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

    <!-- App js-->
    <script src="../../assets/js/app.js"></script>

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
                if (type === 'warning') alertClass = 'alert-warning'; // Thêm kiểu warning

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
            // Kiểm tra xem DataTables đã được khởi tạo chưa trước khi khởi tạo lại.
            // Điều này giải quyết lỗi "Cannot reinitialize DataTable"
            if (!$.fn.DataTable.isDataTable('#datatable')) {
                $('#datatable').DataTable({
                    // Cấu hình DataTables của bạn
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    // "language": {
                    //     "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json" // Nếu bạn cần tiếng Việt và đã có file này
                    // }
                });
            }

            // Xử lý nút "Phân quyền"
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

                const updateRoleUrl = `{{ route('admin.account.toggleUserRole', ['user' => ':userId']) }}`.replace(':userId', userId);

                $.ajax({
                    url: updateRoleUrl,
                    type: 'POST', // Hoặc PUT/PATCH nếu route của bạn định nghĩa
                    data: {
                        new_role: newRole
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Cập nhật giao diện của badge vai trò
                            const roleCell = $(`#user-role-${userId}`);
                            const newRoleBadge = newRole === 'client' ?
                                '<span class="badge bg-info text-dark">Client</span>' :
                                '<span class="badge bg-success">Admin</span>';
                            roleCell.html(newRoleBadge);

                            // Cập nhật data attribute và tooltip của nút
                            button.data('currentRole', newRole);
                            button.attr('title', newRole === 'client' ? 'Chuyển thành Admin' : 'Chuyển thành Client');
                            
                            // Cập nhật màu sắc của nút
                            button.removeClass('btn-warning btn-primary').addClass(newRole === 'client' ? 'btn-warning' : 'btn-primary');
                            
                            // Cập nhật tooltip sau khi thay đổi title
                            button.tooltip('dispose').tooltip();

                            showTemporaryMessage(response.message, 'success');

                            // Tải lại trang sau một khoảng thời gian ngắn để cập nhật hoàn toàn
                            setTimeout(function() {
                                location.reload();
                            }, 500);
                        } else {
                            showTemporaryMessage(response.message || 'Thay đổi vai trò thất bại.', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error toggling role:', xhr.responseText);
                        if (xhr.status === 403) {
                            showTemporaryMessage(xhr.responseJSON.message || 'Bạn không có quyền thực hiện hành động này.', 'error');
                        } else if (xhr.status === 400) {
                            showTemporaryMessage(xhr.responseJSON.message || 'Yêu cầu không hợp lệ.', 'error');
                        } else {
                            showTemporaryMessage('Lỗi hệ thống khi thay đổi vai trò. Vui lòng thử lại.', 'error');
                        }
                    },
                    complete: function() {
                        // Khôi phục trạng thái nút (nếu không reload trang ngay lập tức)
                        button.prop('disabled', false).html(originalHtml);
                    }
                });
            });

            // Xử lý nút "Xóa tạm thời" (Soft Delete)
            $('#datatable').on('submit', 'form[action*="softDeleteUser"]', function(e) {
                e.preventDefault(); // Ngăn chặn form submit mặc định
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
                                $(this).remove(); // Xóa hàng khỏi DOM sau khi fadeOut
                                // Nếu dùng DataTables, bạn có thể cần redraw hoặc reload
                                if ($.fn.DataTable.isDataTable('#datatable')) {
                                    $('#datatable').DataTable().row(form.closest('tr')).remove().draw();
                                }
                            });
                        } else {
                            showTemporaryMessage(response.message || 'Xóa thất bại.', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error soft deleting user:', xhr.responseText);
                        showTemporaryMessage('Lỗi hệ thống khi xóa tạm thời người dùng.', 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html(originalButtonHtml);
                    }
                });
            });

            // Xử lý nút "Reset mật khẩu"
            $('#datatable').on('submit', 'form[action*="resetPassUser"]', function(e) {
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
                            // Không cần reload trang hoặc cập nhật DOM vì mật khẩu không hiển thị
                        } else {
                            showTemporaryMessage(response.message || 'Đặt lại mật khẩu thất bại.', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error resetting password:', xhr.responseText);
                        showTemporaryMessage('Lỗi hệ thống khi đặt lại mật khẩu.', 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html(originalButtonHtml);
                    }
                });
            });

            // Khởi tạo tooltip cho tất cả các phần tử có data-bs-toggle="tooltip" trên trang
            // Điều này cần được gọi sau khi tất cả các phần tử DOM đã được tải.
            // Nếu bạn có các phần tử được thêm động sau này, cần gọi lại .tooltip() cho chúng.
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>


@endpush
