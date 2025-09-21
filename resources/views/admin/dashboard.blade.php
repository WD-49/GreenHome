@extends('layouts.admin')

@section('title')
    {{ $title ?? 'Dashboard' }}
@endsection

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@latest/dist/apexcharts.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@latest"></script>

    {{-- Thông báo cho Login Google, Fb --}}
    <div class="toast-container position-fixed top-0 end-0 p-3">
        @if (session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('warning') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>
    <script>
        // JS cho phần thông báo Login Google FB
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'))
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 5000 // 5 giây
                })
            })
            toastList.forEach(toast => toast.show())
        });
    </script>
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Thống kê bán hàng</h4>
            </div>
        </div>

        <!-- Bộ lọc -->
        <div class="card mb-3">
            <div class="card-body">
                <form id="filter-form" method="GET" action="{{ route('admin.dashboard') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="filter">Loại bộ lọc</label>
                            <select name="filter" id="filter" class="form-control">
                                <option value="day" {{ $filter == 'day' ? 'selected' : '' }}>Ngày</option>
                                <option value="month" {{ $filter == 'month' ? 'selected' : '' }}>Tháng</option>
                                <option value="year" {{ $filter == 'year' ? 'selected' : '' }}>Năm</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="start_date">Từ
                                {{ $filter == 'day' ? 'ngày' : ($filter == 'month' ? 'tháng' : 'năm') }}</label>
                            <input type="{{ $filter == 'day' ? 'date' : ($filter == 'month' ? 'month' : 'number') }}"
                                name="start_date" id="start_date" class="form-control" value="{{ $startDateStr }}"
                                {{ $filter == 'year' ? 'min="' . (now()->year - 20) . '" max="' . now()->year . '"' : '' }}>
                        </div>
                        <div class="col-md-3">
                            <label for="end_date">Đến
                                {{ $filter == 'day' ? 'ngày' : ($filter == 'month' ? 'tháng' : 'năm') }}</label>
                            <input type="{{ $filter == 'day' ? 'date' : ($filter == 'month' ? 'month' : 'number') }}"
                                name="end_date" id="end_date" class="form-control" value="{{ $endDateStr }}"
                                {{ $filter == 'year' ? 'min="' . (now()->year - 20) . '" max="' . now()->year . '"' : '' }}>
                        </div>
                        <div class="col-md-3 align-self-end">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                        </div>
                    </div>
                    <p class="text-muted mt-2">
                        Chọn loại bộ lọc:
                        <span id="filter-hint">
                            @if ($filter == 'day')
                                30 ngày gần nhất (tối đa 31 ngày)
                            @elseif ($filter == 'month')
                                12 tháng trong năm nay (tối đa 12 tháng)
                            @else
                                10 năm tính từ năm nay (tối đa 10 năm)
                            @endif
                        </span>
                    </p>
                </form>
            </div>
        </div>

        <!-- Thông báo lỗi -->
        <div id="error-alert" class="alert alert-danger d-none" role="alert"></div>

        <!-- 4 Card Thống kê -->
        <div class="row g-3 mb-3">
            <div class="col-md-3 col-xl-3">
                <div class="card border-primary shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-cart fs-3 text-primary mb-2"></i>
                        <div class="fs-14 mb-2 text-muted">Đơn hàng</div>
                        <div class="fs-24 fw-semibold text-primary" id="new-orders-total">0</div>
                        <div id="new-orders-empty" class="text-muted mt-2 d-none">Không có dữ liệu</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-xl-3">
                <div class="card border-primary shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle fs-3 text-primary mb-2"></i>
                        <div class="fs-14 mb-2 text-muted">Đơn hàng đã hoàn thành</div>
                        <div class="fs-24 fw-semibold text-primary" id="sales-total">0</div>
                        <div id="sales-empty" class="text-muted mt-2 d-none">Không có dữ liệu</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-xl-3">
                <div class="card border-primary shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-exchange fs-3 text-primary mb-2"></i>
                        <div class="fs-14 mb-2 text-muted">Doanh thu</div>
                        <div class="fs-24 fw-semibold text-primary" id="revenue-total">0 ₫</div>
                        <div id="revenue-empty" class="text-muted mt-2 d-none">Không có dữ liệu</div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-xl-3">
                <div class="card border-primary shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-people fs-3 text-primary mb-2"></i>
                        <div class="fs-14 mb-2 text-muted">Khách hàng mới</div>
                        <div class="fs-24 fw-semibold text-primary" id="new-users-total">0</div>
                        <div id="new-users-empty" class="text-muted mt-2 d-none">Không có dữ liệu</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ -->
        <div class="row">
            <div class="col-md-12 col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="border border-primary rounded-2 me-2 widget-icons-sections">
                                <i data-feather="git-commit" class="widgets-icons"></i>
                            </div>
                            <h5 class="card-title mb-0">Doanh thu theo thời gian</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="chart-money" class="apex-charts"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xl-4">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                                <i data-feather="crosshair" class="widgets-icons"></i>
                            </div>
                            <h5 class="card-title mb-0">Khách hàng tiềm năng</h5>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-traffic mb-0">
                                <thead>
                                    <tr>
                                        <th>Khách hàng</th>
                                        <th>Đơn hàng</th>
                                        <th>Tổng chi tiêu</th>
                                    </tr>
                                </thead>
                                <tbody id="top-customers"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Top Selling Products -->
            <div class="col-md-6 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                                <i data-feather="cpu" class="widgets-icons"></i>
                            </div>
                            <h5 class="card-title mb-0">Sản phẩm bán chạy</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-group custom-group" id="top-selling-products"></ul>
                    </div>
                </div>
            </div>

            <!-- Top Rated Products -->
            <div class="col-md-6 col-xl-6">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                                <i data-feather="cpu" class="widgets-icons"></i>
                            </div>
                            <h5 class="card-title mb-0">Sản phẩm đánh giá cao nhất</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-group custom-group" id="top-rated-products"></ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                                <i data-feather="crosshair" class="widgets-icons"></i>
                            </div>
                            <h5 class="card-title mb-0">Đơn hàng hiện tại</h5>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-traffic mb-0">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Khách hàng</th>
                                        <th>Tổng tiền</th>
                                        <th colspan="2">Trạng thái đơn hàng</th>
                                        <th colspan="2">Trạng thái thanh toán</th>
                                        <th>Ngày đặt</th>
                                        <th>Chi tiết</th>
                                    </tr>
                                </thead>
                                <tbody id="current-orders"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript xử lý bộ lọc và biểu đồ -->
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Cập nhật hint khi thay đổi bộ lọc
                const filterSelect = document.querySelector('#filter');
                const filterHint = document.querySelector('#filter-hint');
                const startLabel = document.querySelector('label[for="start_date"]');
                const endLabel = document.querySelector('label[for="end_date"]');
                const startInput = document.querySelector('#start_date');
                const endInput = document.querySelector('#end_date');
                const currentYear = new Date().getFullYear();

                filterSelect.addEventListener('change', () => {
                    const filterValue = filterSelect.value;
                    let hintText = '';
                    let typeText = '';
                    let inputType = '';

                    if (filterValue === 'day') {
                        hintText = '30 ngày gần nhất (tối đa 31 ngày)';
                        typeText = 'ngày';
                        inputType = 'date';
                    } else if (filterValue === 'month') {
                        hintText = '12 tháng trong năm nay (tối đa 12 tháng)';
                        typeText = 'tháng';
                        inputType = 'month';
                    } else {
                        hintText = '10 năm tính từ năm nay (tối đa 10 năm)';
                        typeText = 'năm';
                        inputType = 'number';
                    }

                    filterHint.textContent = hintText;
                    startLabel.textContent = `Từ ${typeText}`;
                    endLabel.textContent = `Đến ${typeText}`;
                    startInput.type = inputType;
                    endInput.type = inputType;

                    if (filterValue === 'year') {
                        startInput.min = currentYear - 20;
                        startInput.max = currentYear;
                        endInput.min = currentYear - 20;
                        endInput.max = currentYear;
                    } else {
                        startInput.removeAttribute('min');
                        startInput.removeAttribute('max');
                        endInput.removeAttribute('min');
                        endInput.removeAttribute('max');
                    }

                    // Clear values to use defaults
                    startInput.value = '';
                    endInput.value = '';

                    document.querySelector('#filter-form').submit();
                });

                // Khởi tạo biểu đồ Sales Report
                let salesChart = null;
                const initSalesChart = (labels, data) => {
                    const filter = document.querySelector('#filter').value;
                    // Kiểm tra dữ liệu để tránh lỗi
                    if (!labels || !data || labels.length !== data.length) {
                        console.warn('Dữ liệu biểu đồ không hợp lệ:', {
                            labels,
                            data
                        });
                        return;
                    }

                    // Tính toán chiều rộng biểu đồ dựa trên số lượng nhãn
                    const chartWidth = filter === 'day' ? Math.max(100, labels.length * 10) + '%' : '100%';

                    const options = {
                        series: [{
                            name: 'Doanh thu',
                            data: data
                        }],
                        chart: {
                            type: 'line',
                            height: 350,
                            width: chartWidth,
                            zoom: {
                                enabled: filter === 'day',
                                type: 'x',
                                autoScaleYaxis: true
                            },
                            toolbar: {
                                show: filter === 'day',
                                tools: {
                                    zoom: true,
                                    zoomin: true,
                                    zoomout: true,
                                    pan: true,
                                    reset: true
                                }
                            },
                            animations: {
                                enabled: filter === 'day',
                                easing: 'easeinout',
                                speed: 800,
                                animateGradually: {
                                    enabled: true,
                                    delay: 150
                                }
                            },
                            redrawOnParentResize: false,
                            redrawOnWindowResize: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        markers: {
                            size: filter === 'day' ? 4 : 6,
                            hover: {
                                size: 8
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        xaxis: {
                            type: 'category',
                            categories: labels,
                            labels: {
                                rotate: 0,
                                rotateAlways: false,
                                trim: false,
                                style: {
                                    fontSize: '12px'
                                },
                                formatter: function(val) {
                                    if (filter === 'year') return val;
                                    if (filter === 'month') return val;
                                    return new Date(val).toLocaleDateString('vi-VN', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric'
                                    });
                                },
                                tickAmount: filter === 'day' ? Math.ceil(labels.length / 5) : undefined
                            },
                            scrollbar: {
                                enabled: filter === 'day'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Doanh thu (VNĐ)'
                            },
                            labels: {
                                formatter: function(val) {
                                    return Number(val).toLocaleString('vi-VN') + ' ₫';
                                }
                            }
                        },
                        tooltip: {
                            enabled: true,
                            shared: false,
                            intersect: false,
                            followCursor: true,
                            fixed: {
                                enabled: filter !== 'day',
                                position: 'topRight',
                                offsetX: 0,
                                offsetY: 0
                            },
                            onDatasetHover: {
                                highlightDataSeries: false
                            },
                            marker: {
                                show: filter === 'day'
                            },
                            x: {
                                show: true,
                                formatter: function(val, {
                                    dataPointIndex
                                }) {
                                    const date = labels[dataPointIndex];
                                    if (!date) return '';
                                    if (filter === 'year') return date;
                                    if (filter === 'month') return date;
                                    return new Date(date).toLocaleDateString('vi-VN', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric'
                                    });
                                }
                            },
                            y: {
                                formatter: function(val) {
                                    return Number(val).toLocaleString('vi-VN') + ' ₫';
                                }
                            }
                        },
                        colors: ['#6366f1'],
                        fill: {
                            opacity: 1
                        }
                    };

                    if (salesChart) salesChart.destroy();
                    salesChart = new ApexCharts(document.querySelector('#chart-money'), options);
                    salesChart.render();
                };

                // Hàm gọi API
                const loadDashboardData = async () => {
                    const filter = document.querySelector('#filter').value;
                    const startDate = document.querySelector('#start_date').value;
                    const endDate = document.querySelector('#end_date').value;

                    // Hiển thị loading
                    const cardBodies = document.querySelectorAll('.card-body');
                    cardBodies.forEach(body => {
                        body.insertAdjacentHTML('beforeend',
                            '<div class="loading-spinner">Đang tải...</div>');
                    });
                    const errorAlert = document.querySelector('#error-alert');
                    errorAlert.classList.add('d-none');

                    try {
                        const response = await fetch(
                            `{{ route('admin.dashboard.data') }}?filter=${filter}&start_date=${startDate}&end_date=${endDate}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                        const result = await response.json();

                        // Xóa loading
                        document.querySelectorAll('.loading-spinner').forEach(spinner => spinner.remove());

                        if (!result.success) {
                            errorAlert.textContent = result.message;
                            errorAlert.classList.remove('d-none');
                            return;
                        }

                        const data = result.data;

                        // Cập nhật tổng số
                        document.querySelector('#new-orders-total').textContent = data.new_orders?.total ?? 0;
                        document.querySelector('#sales-total').textContent = data.sales?.total ?? 0;
                        document.querySelector('#revenue-total').textContent = Number(data.revenue?.total ?? 0)
                            .toLocaleString('vi-VN') + ' ₫';
                        document.querySelector('#new-users-total').textContent = data.new_users?.total ?? 0;

                        // Cập nhật thông báo dữ liệu trống
                        document.querySelector('#new-orders-empty').classList.toggle('d-none', !data.new_orders
                            ?.empty);
                        document.querySelector('#sales-empty').classList.toggle('d-none', !data.sales?.empty);
                        document.querySelector('#revenue-empty').classList.toggle('d-none', !data.revenue
                            ?.empty);
                        document.querySelector('#new-users-empty').classList.toggle('d-none', !data.new_users
                            ?.empty);

                        // Chuyển dữ liệu doanh thu sang số
                        const revenueData = data.revenue?.data.map(Number) ?? [];

                        // Cập nhật biểu đồ Sales Report
                        initSalesChart(data.labels ?? [], revenueData);

                        // Cập nhật bảng Khách hàng tiềm năng
                        const topCustomersTbody = document.querySelector('#top-customers');
                        topCustomersTbody.innerHTML = '';
                        if (!Array.isArray(data.top_customers) || data.top_customers.length === 0) {
                            topCustomersTbody.innerHTML =
                                '<tr><td colspan="3" class="text-center">Không có dữ liệu</td></tr>';
                        } else {
                            data.top_customers.forEach(customer => {
                                topCustomersTbody.innerHTML += `
    <tr>
        <td>${customer.name || 'N/A'}</td>
        <td class="text-center">${customer.order_count || 0}</td>
        <td>${Number(customer.total_spent || 0).toLocaleString('vi-VN')} ₫</td>
    </tr>`;
                            });
                        }

                        // Cập nhật bảng Sản phẩm bán chạy
                        const topSellingProducts = document.querySelector('#top-selling-products');
                        topSellingProducts.innerHTML = '';
                        if (!Array.isArray(data.top_selling_products) || data.top_selling_products.length ===
                            0) {
                            topSellingProducts.innerHTML =
                                '<li class="list-group-item text-center">Không có dữ liệu</li>';
                        } else {
                            data.top_selling_products.forEach(product => {
                                topSellingProducts.innerHTML += `
    <li class="list-group-item align-items-center d-flex justify-content-between">
        <div class="product-list d-flex align-items-center">
            <img class="avatar-md p-1 rounded-circle bg-primary-subtle img-fluid me-3"
                src="/storage/${product.image || 'default.png'}" alt="product-image">
            <div class="product-body align-self-center">
                <h6 class="m-0 fw-semibold">${product.product_name || 'N/A'}</h6>
                <p class="mb-0 mt-1 text-muted">${product.product_attribute || ''}</p>
            </div>
        </div>
        <div class="product-price">
            <h6 class="m-0 fw-semibold">${Number(product.product_price || 0).toLocaleString('vi-VN')} ₫</h6>
            <p class="mb-0 mt-1 text-muted">${product.sold || 0} sản phẩm đã bán</p>
        </div>
    </li>`;
                            });
                        }

                        // Cập nhật bảng Sản phẩm đánh giá cao nhất
                        const topRatedProducts = document.querySelector('#top-rated-products');
                        topRatedProducts.innerHTML = '';
                        if (!Array.isArray(data.top_rated_products) || data.top_rated_products.length === 0) {
                            topRatedProducts.innerHTML =
                                '<li class="list-group-item text-center">Không có dữ liệu</li>';
                        } else {
                            data.top_rated_products.forEach(product => {
                                topRatedProducts.innerHTML += `
    <li class="list-group-item align-items-center d-flex justify-content-between">
        <div class="product-list d-flex align-items-center">
            <img class="avatar-md p-1 rounded-circle bg-primary-subtle img-fluid me-3"
                src="/storage/${product.image || 'storage/default.png'}" alt="product-image">
            <div class="product-body align-self-center">
                <h6 class="m-0 fw-semibold">${product.product_name || 'N/A'}</h6>
                <p class="mb-0 mt-1 text-muted">${product.attribute_name || ''}</p>
            </div>
        </div>
        <div class="product-price">
            <h6 class="m-0 fw-semibold">${Number(product.rating || 0).toFixed(1)} ⭐</h6>
            <p class="mb-0 mt-1 text-muted">${product.review_count || 0} đánh giá</p>
        </div>
    </li>`;
                            });
                        }

                        // Cập nhật bảng Đơn hàng hiện tại
                        const currentOrdersTbody = document.querySelector('#current-orders');
                        currentOrdersTbody.innerHTML = '';
                        if (!Array.isArray(data.current_orders) || data.current_orders.length === 0) {
                            currentOrdersTbody.innerHTML =
                                '<tr><td colspan="8" class="text-center">Không có dữ liệu</td></tr>';
                        } else {
                            data.current_orders.forEach(order => {
                                const orderStatusClass = {
                                    'Chưa xác nhận': 'text-warning',
                                    'Xác nhận': 'text-info',
                                    'Đang vận chuyển': 'text-primary',
                                    'Giao hàng thành công': 'text-success',
                                    'Hủy đơn': 'text-danger',
                                    'Đã nhận hàng': 'text-success',
                                    'Đã hoàn hàng': 'text-primary'
                                } [order.order_status] || 'text-muted';
                                const paymentStatusClass = {
                                    'pending': 'text-warning',
                                    'paid': 'text-success',
                                    'failed': 'text-danger',
                                    'refunded': 'text-info'
                                } [order.payment_status] || 'text-muted';
                                currentOrdersTbody.innerHTML += `
    <tr>
        <td><a href="javascript:void(0);" class="text-reset">${order.sku || 'N/A'}</a></td>
        <td class="d-flex align-items-center">
            <img src="/storage/${order.user_image || 'public/storage/default-avatar.jpg'}"
                class="avatar avatar-sm rounded-2 me-3" />
            <p class="mb-0 fw-medium">${order.user_name || 'N/A'}</p>
        </td>
        <td>${Number(order.total_amount || 0).toLocaleString('vi-VN')} ₫</td>
        <td colspan="2">
            <p class="mb-0 ${orderStatusClass}">${order.order_status || 'N/A'}</p>
        </td>
        <td colspan="2">
            <p class="mb-0 ${paymentStatusClass}">${order.payment_status_translated || 'N/A'}</p>
        </td>
        <td>${order.created_at ? new Date(order.created_at).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit',
            year: 'numeric' }) : 'N/A'}</td>
        <td>
            <a href="/admin/orders/show/${order.id}" class="text-primary">
                <i class="mdi mdi-eye fs-18 rounded-2 border p-1"></i>
            </a>
        </td>
    </tr>`;
                            });
                        }
                    } catch (error) {
                        // Xóa loading
                        document.querySelectorAll('.loading-spinner').forEach(spinner => spinner.remove());
                        errorAlert.textContent = error.message || 'Không thể tải dữ liệu. Vui lòng thử lại.';
                        errorAlert.classList.remove('d-none');
                    }
                };

                // Gọi API khi form submit
                document.querySelector('#filter-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    const filter = document.querySelector('#filter').value;
                    const startDate = document.querySelector('#start_date').value;
                    const endDate = document.querySelector('#end_date').value;
                    console.log('Filter:', filter, 'Start Date:', startDate, 'End Date:', endDate);
                    loadDashboardData();
                });

                // Gọi API lần đầu
                loadDashboardData();
            });
        </script>
    @endpush

    <!-- CSS tùy chỉnh -->
    <style>
        .loading-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 14px;
            color: #6366f1;
        }

        .card-body {
            padding: 1.5rem;
            position: relative;
        }

        .fs-24 {
            font-size: 1.5rem;
        }

        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .widget-icons-sections {
            padding: 8px;
        }

        .widgets-icons {
            width: 20px;
            height: 20px;
        }

        #chart-money {
            overflow-x: auto;
            min-width: 100%;
        }

        .table-responsive {
            min-height: 100px;
        }

        .avatar-md {
            width: 50px;
            height: 50px;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
        }

        .list-group-item {
            border: none;
            padding: 0.75rem 1.25rem;
        }

        .mdi-eye {
            color: #6366f1;
            transition: color 0.2s;
        }

        .mdi-eye:hover {
            color: #3b3f99;
        }
    </style>
@endsection
