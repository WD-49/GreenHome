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
    <style>
        .btn-action-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.5;
            border-radius: 0.2rem;
            margin-right: 0.25rem;
            /* Khoảng cách giữa các nút */
        }

        /* Để nút cuối cùng trong nhóm không có margin-right */
        .btn-action-sm:last-child {
            margin-right: 0;
        }
    </style>
@endpush
@section('content')
    <!-- Start Content-->
    <div class="container-xxxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Quản lý đơn hàng</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <h6 class="breadcrumb-item active">Home / Đơn hàng / Quản lý đơn hàng</h6>
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
                        <h5 class="card-title mb-0">Danh sách đơn hàng</h5>
                        <div>
                            <a href="{{ route('admin.orders.trash') }}" class="btn btn-danger shadow-sm">
                                <i class="fas fa-trash-restore fa-sm text-white-50"></i> Thùng rác
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <table id="datatable" class="table table-bordered dt-responsive table-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Tên người nhận</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Phương thức</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $index => $order)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>#{{ $order->sku ?? $order->id }}</td>
                                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        <td>{{ $order->shipping_name }}
                                        </td>
                                        <td>
                                            {{ $order->created_at }}
                                        </td>
                                        <td>
                                            {{ number_format($order->total_amount, 0) }} VND
                                        </td>
                                        <td>
                                            {{ $order->paymentMethod->name }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge rounded-pill
                                        @if ($order->order_status == 'Giao hàng thành công') bg-success
                                        @elseif ($order->order_status == 'Chờ xác nhận') bg-warning text-dark
                                        @elseif ($order->order_status == 'Hủy đơn') bg-danger
                                        @else bg-info text-dark @endif">
                                                {{ $order->order_status ?? 'Chưa cập nhật' }}
                                            </span>
                                        </td>

                                        <td>
                                            {{-- Nhóm các nút hành động lại --}}
                                            <div class="btn-group" role="group" aria-label="Order Actions">
                                                {{-- Nút Sửa --}}
                                                <a href="{{ route('admin.orders.edit', $order->id) }}"
                                                    class="btn btn-action-sm btn-primary" title="Sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- Nút Xem chi tiết --}}
                                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                                    class="btn btn-action-sm btn-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                {{-- Nút Hủy đơn hàng (hiển thị dưới dạng một nút thông thường, không phải dropdown-item) --}}
                                                @if (method_exists($order, 'canBeCancelled') && $order->canBeCancelled())
                                                    <button type="button" class="btn btn-action-sm btn-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cancelOrderModal-{{ $order->id }}"
                                                        title="Hủy đơn hàng">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                @endif

                                                {{-- Form Xóa (Thùng rác) --}}
                                                {{-- Giữ form submit để xử lý delete --}}
                                                <form action="{{ route('admin.orders.destroy', $order->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này (đưa vào thùng rác)?')"
                                                    class="d-inline soft-delete-order-form"> {{-- Thêm class cho form --}}
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-action-sm btn-danger"
                                                        title="Xóa (Thùng rác)">
                                                        <i class="fas fa-trash"></i>
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
@endpush
