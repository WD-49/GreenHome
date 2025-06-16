@extends('layouts.admin')

@section('title')
    {{ $title }}
@endsection


@section('content')
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Ecommerce</h4>
        </div>
    </div>

    <!-- start row -->
    <div class="row">
        <div class="col-md-12 col-xl-12">
            <div class="row g-3">
                <div class="col-md-3 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-14 mb-1 text-muted">Đơn hàng hôm nay</div>
                                    </div>

                                    <div class="d-flex align-items-baseline mb-0">
                                        <div class="fs-20 mb-0 me-2 fw-semibold text-dark" id="orders-today"></div>
                                    </div>
                                </div>

                                <div class="col-6 d-flex justify-content-center align-items-center">
                                    <div id="orders-report-per-week" style="min-height:200px"></div>
                                </div>
                            </div>

                            <p class="d-flex align-content-center border-top mb-0 pt-3 mt-3">

                                <span class="fw-medium me-1 d-flex" id="orders-status"></span>
                                <span class="me-2 d-flex align-content-center fw-medium text-success">
                                    <span id="orders-percent">+0%</span>
                                    {{-- <i data-feather="trending-up" class="ms-2" style="height: 22px; width: 22px;"></i> --}}
                                </span>
                                <span class="fw-medium me-1 d-flex">so với hôm qua</span>
                                <span class="fw-medium me-1 d-flex" id="orders-yesterday">
                                </span>

                            </p>

                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-14 mb-1 text-muted">Doanh thu hôm nay</div>
                                    </div>

                                    <div class="d-flex align-items-baseline mb-0">
                                        <div class="fs-20 mb-0 me-2 fw-semibold text-dark" id="sales-today"></div>
                                    </div>
                                </div>

                                <div class="col-6 d-flex justify-content-center align-items-center">
                                    <div id="sales-report-per-week" class="apex-charts"></div>
                                </div>
                            </div>

                            <p class="d-flex align-content-center border-top mb-0 pt-3 mt-3">
                                <span class="fw-medium me-1 d-flex" id="sales-status"></span>
                                <span class="me-2 d-flex align-content-center fw-medium text-danger" id="sales-percent">
                                    %
                                </span>
                                <span class="fw-medium me-1 d-flex">so với hôm qua</span>
                                <span class="fw-medium me-1 d-flex" id="sales-yesterday"></span>
                                {{-- Doanh thu hôm qua --}}
                            </p>

                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-14 mb-1 text-muted">Khách hàng mới hôm nay</div>
                                    </div>
                                    <div class="d-flex align-items-baseline mb-0">
                                        <div class="fs-20 mb-0 me-2 fw-semibold text-dark" id="new-customers-today"></div>
                                    </div>
                                </div>
                                <div class="col-6 d-flex justify-content-center align-items-center">
                                    <div id="new-customers-report-per-week" style="min-height:80px"></div>
                                </div>
                            </div>
                            <p class="d-flex align-content-center border-top mb-0 pt-3 mt-3">
                                <span class="fw-medium me-1 d-flex" id="new-customers-status"></span>
                                <span class="me-2 d-flex align-content-center fw-medium text-success"
                                    id="new-customers-percent">+0%</span>
                                <span class="fw-medium me-1 d-flex">so với hôm qua</span>
                                <span class="fw-medium me-1 d-flex" id="new-customers-yesterday"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-14 mb-1 text-muted">Sản phẩm đã bán hôm nay</div>
                                    </div>
                                    <div class="d-flex align-items-baseline mb-0">
                                        <div class="fs-20 mb-0 me-2 fw-semibold text-dark" id="products-sold-today"></div>
                                    </div>
                                </div>
                                <div class="col-6 d-flex justify-content-center align-items-center">
                                    <div id="products-sold-report-per-week" style="min-height:80px"></div>
                                </div>
                            </div>
                            <p class="d-flex align-content-center border-top mb-0 pt-3 mt-3">
                                <span class="fw-medium me-1 d-flex" id="products-sold-status"></span>
                                <span class="me-2 d-flex align-content-center fw-medium text-success"
                                    id="products-sold-percent">+0%</span>
                                <span class="fw-medium me-1 d-flex">so với hôm qua</span>
                                <span class="fw-medium me-1 d-flex" id="products-sold-yesterday"></span>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div> <!-- end sales -->
    </div> <!-- end row -->


    <!-- Sales Chart -->
    <div class="row">
        <div class="col-md-12 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                            <i data-feather="git-commit" class="widgets-icons"></i>
                        </div>
                        <h5 class="card-title mb-0">Sales Report</h5>
                    </div>
                </div>

                <div class="card-body">
                    <div id="sale-report" class="apex-charts"></div>
                </div>
            </div>
        </div>


        {{-- <div class="col-md-12 col-xl-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                            <i data-feather="pie-chart" class="widgets-icons"></i>
                        </div>
                        <h5 class="card-title mb-0">Sales by Country</h5>
                    </div>
                </div>

                <div class="card-body">
                    <div id="sales-country" class="apex-charts"></div>
                </div>
            </div>
        </div> --}}
    </div>


    <div class="row">
        <!-- Top Selling Products -->
        <div class="col-md-6 col-xl-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                            <i data-feather="cpu" class="widgets-icons"></i>
                        </div>
                        <h5 class="card-title mb-0">Top Selling Products</h5>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group custom-group" id="top-selling-products">
                        <!-- Sẽ render động ở JS -->
                    </ul>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="col-md-6 col-xl-8">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                            <i data-feather="bar-chart" class="widgets-icons"></i>
                        </div>
                        <h5 class="card-title mb-0">Repeat Customer Rate</h5>
                    </div>
                    <select id="repeat-range" class="form-select form-select-sm" style="width: 160px;">
                        <option value="day">10 ngày gần nhất</option>
                        <option value="week">10 tuần gần nhất</option>
                        <option value="month">12 tháng gần nhất</option>
                    </select>
                </div>

                <div class="card-body">
                    <div id="repeat-customer-per-week" class="apex-charts"></div>
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
                        <h5 class="card-title mb-0">Recent Order</h5>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-traffic mb-0">

                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Price</th>
                                    <th>Created</th>
                                    <th>Modified</th>
                                    <th colspan="2">Status</th>
                                </tr>
                            </thead>

                            <tr>
                                <td>
                                    <a href="javascript:void(0);" class="text-reset">#3413</a>
                                </td>
                                <td class="d-flex align-items-center">
                                    <img src="assets/images/users/user-12.jpg" class="avatar avatar-sm rounded-2 me-3" />
                                    <p class="mb-0 fw-medium">Richard Dom</p>
                                </td>
                                <td>
                                    <p class="mb-0">82</p>
                                </td>
                                <td>
                                    <p class="mb-0">$480.00</p>
                                </td>
                                <td>
                                    <p class="mb-0">August 09, 2023</p>
                                </td>
                                <td>
                                    <p class="mb-0">August 18, 2023</p>
                                </td>
                                <td>
                                    <p class="text-danger mb-0">Cancelled</p>
                                </td>
                                <td>
                                    <a href="#"><i
                                            class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i></a>
                                    <a href="#"><i
                                            class="mdi mdi-delete text-muted fs-18 rounded-2 border p-1"></i></a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <a href="javascript:void(0);" class="text-reset">#4125</a>
                                </td>
                                <td class="d-flex align-items-center">
                                    <img src="assets/images/users/user-11.jpg" class="avatar avatar-sm rounded-2 me-3" />
                                    <p class="mb-0 fw-medium">Randal Dare</p>
                                </td>
                                <td>
                                    <p class="mb-0">93</p>
                                </td>
                                <td>
                                    <p class="mb-0">$568.00</p>
                                </td>
                                <td>
                                    <p class="mb-0">January 19, 2023</p>
                                </td>
                                <td>
                                    <p class="mb-0">March 09, 2023</p>
                                </td>
                                <td>
                                    <p class="text-muted mb-0">Refunded</p>
                                </td>
                                <td>
                                    <a href="#"><i
                                            class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i></a>
                                    <a href="#"><i
                                            class="mdi mdi-delete text-muted fs-18 rounded-2 border p-1"></i></a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <a href="javascript:void(0);" class="text-reset">#6532</a>
                                </td>
                                <td class="d-flex align-items-center">
                                    <img src="assets/images/users/user-13.jpg" class="avatar avatar-sm rounded-2 me-3" />
                                    <p class="mb-0 fw-medium">Bickle Bob</p>
                                </td>
                                <td>
                                    <p class="mb-0">56</p>
                                </td>
                                <td>
                                    <p class="mb-0">$398.00</p>
                                </td>
                                <td>
                                    <p class="mb-0">April 25, 2023</p>
                                </td>
                                <td>
                                    <p class="mb-0">June 21, 2023</p>
                                </td>
                                <td>
                                    <p class="text-danger mb-0">Cancelled</p>
                                </td>
                                <td>
                                    <a href="#"><i
                                            class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i></a>
                                    <a href="#"><i
                                            class="mdi mdi-delete text-muted fs-18 rounded-2 border p-1"></i></a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <a href="javascript:void(0);" class="text-reset">#7405</a>
                                </td>
                                <td class="d-flex align-items-center">
                                    <img src="assets/images/users/user-14.jpg" class="avatar avatar-sm rounded-2 me-3" />
                                    <p class="mb-0 fw-medium">Emma Wilson</p>
                                </td>
                                <td>
                                    <p class="mb-0">68</p>
                                </td>
                                <td>
                                    <p class="mb-0">$652.00</p>
                                </td>
                                <td>
                                    <p class="mb-0">September 24, 2023</p>
                                </td>
                                <td>
                                    <p class="mb-0">November 13, 2023</p>
                                </td>
                                <td>
                                    <p class="text-muted mb-0">Refunded</p>
                                </td>
                                <td>
                                    <a href="#"><i
                                            class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i></a>
                                    <a href="#"><i
                                            class="mdi mdi-delete text-muted fs-18 rounded-2 border p-1"></i></a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <a href="javascript:void(0);" class="text-reset">#4526</a>
                                </td>
                                <td class="d-flex align-items-center">
                                    <img src="assets/images/users/user-15.jpg" class="avatar avatar-sm rounded-2 me-3" />
                                    <p class="mb-0 fw-medium">Hugh Jackma</p>
                                </td>
                                <td>
                                    <p class="mb-0">52</p>
                                </td>
                                <td>
                                    <p class="mb-0">$746.00</p>
                                </td>
                                <td>
                                    <p class="mb-0">July 28, 2023</p>
                                </td>
                                <td>
                                    <p class="mb-0">August 21, 2023</p>
                                </td>
                                <td>
                                    <p class="text-danger mb-0">Cancelled</p>
                                </td>
                                <td>
                                    <a href="#"><i
                                            class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i></a>
                                    <a href="#"><i
                                            class="mdi mdi-delete text-muted fs-18 rounded-2 border p-1"></i></a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <a href="javascript:void(0);" class="text-reset">#1054</a>
                                </td>
                                <td class="d-flex align-items-center">
                                    <img src="assets/images/users/user-12.jpg" class="avatar avatar-sm rounded-2 me-3" />
                                    <p class="mb-0 fw-medium">Angelina Hose</p>
                                </td>
                                <td>
                                    <p class="mb-0">45</p>
                                </td>
                                <td>
                                    <p class="mb-0">$205.00</p>
                                </td>
                                <td>
                                    <p class="mb-0">June 09, 2023</p>
                                </td>
                                <td>
                                    <p class="mb-0">August 25, 2023</p>
                                </td>
                                <td>
                                    <p class="text-danger mb-0">Cancelled</p>
                                </td>
                                <td>
                                    <a href="#"><i
                                            class="mdi mdi-pencil text-muted fs-18 rounded-2 border p-1 me-1"></i></a>
                                    <a href="#"><i
                                            class="mdi mdi-delete text-muted fs-18 rounded-2 border p-1"></i></a>
                                </td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/ecommerce-dashboard.init.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/admin/dashboard/data')
                .then(res => res.json())
                .then(data => {
                    // Hiển thị số đơn hàng hôm nay
                    document.getElementById('orders-today').textContent = data.orders_today;
                    document.getElementById('orders-yesterday').textContent = data.orders_yesterday;

                    // Hiển thị % tăng/giảm
                    const percentElem = document.getElementById('orders-percent');
                    const statusElem = document.getElementById('orders-status');
                    const percent = data.orders_percent_change;

                    if (percent > 0) {
                        percentElem.textContent = `+${percent}% `;
                        percentElem.classList.add('text-success');
                        percentElem.classList.remove('text-danger');
                        statusElem.textContent = 'Tăng';
                    } else if (percent < 0) {
                        percentElem.textContent = `${percent}% `;
                        percentElem.classList.add('text-danger');
                        percentElem.classList.remove('text-success');
                        statusElem.textContent = 'Giảm';
                    } else {
                        percentElem.textContent = '0% ';
                        percentElem.classList.remove('text-success', 'text-danger');
                        statusElem.textContent = 'No Change';
                    }

                    var options = {
                        chart: {
                            type: 'bar',
                            height: 80,
                            sparkline: {
                                enabled: true
                            }
                        },
                        series: [{
                            name: 'Đơn hàng',
                            data: data.orders_last_7_days
                        }],
                        xaxis: {
                            categories: data.orders_last_7_days_labels
                        },
                        colors: ['#556ee6'],
                        tooltip: {
                            enabled: true,
                            y: {
                                formatter: function(val) {
                                    return val + " đơn";
                                }
                            }
                        }
                    };
                    var chart = new ApexCharts(document.querySelector("#orders-report-per-week"), options);
                    console.log(data.orders_last_7_days);
                    console.log(data.orders_last_7_days_labels);
                    chart.render();

                    // Doanh thu hôm nay
                    document.getElementById('sales-today').textContent = data.sales_today.toLocaleString();
                    document.getElementById('sales-yesterday').textContent = data.sales_yesterday
                        .toLocaleString();

                    // % tăng/giảm doanh thu
                    const salesPercentElem = document.getElementById('sales-percent');
                    const salesStatusElem = document.getElementById('sales-status');
                    const salesPercent = data.sales_percent_change;

                    if (salesPercent > 0) {
                        salesPercentElem.textContent = `+${salesPercent}% `;
                        salesPercentElem.classList.add('text-success');
                        salesPercentElem.classList.remove('text-danger');
                        salesStatusElem.textContent = 'Tăng';
                    } else if (salesPercent < 0) {
                        salesPercentElem.textContent = `${salesPercent}% `;
                        salesPercentElem.classList.add('text-danger');
                        salesPercentElem.classList.remove('text-success');
                        salesStatusElem.textContent = 'Giảm';
                    } else {
                        salesPercentElem.textContent = '0% ';
                        salesPercentElem.classList.remove('text-success', 'text-danger');
                        salesStatusElem.textContent = 'Không đổi';
                    }

                    // Biểu đồ doanh thu 7 ngày
                    var salesOptions = {
                        chart: {
                            type: 'bar',
                            height: 80,
                            sparkline: {
                                enabled: true
                            }
                        },
                        series: [{
                            name: 'Doanh thu',
                            data: data.sales_last_7_days
                        }],
                        xaxis: {
                            categories: data.sales_last_7_days_labels
                        },
                        colors: ['#f46a6a'],
                        tooltip: {
                            enabled: true,
                            y: {
                                formatter: function(val) {
                                    return val.toLocaleString() + " đ";
                                }
                            }
                        }
                    };
                    var salesChart = new ApexCharts(document.querySelector("#sales-report-per-week"),
                        salesOptions);
                    salesChart.render();

                    // Khách hàng mới hôm nay
                    document.getElementById('new-customers-today').textContent = data.new_customers_today;
                    document.getElementById('new-customers-yesterday').textContent = data
                        .new_customers_yesterday;
                    const ncPercentElem = document.getElementById('new-customers-percent');
                    const ncStatusElem = document.getElementById('new-customers-status');
                    const ncPercent = data.new_customers_percent;
                    if (ncPercent > 0) {
                        ncPercentElem.textContent = `+${ncPercent}% `;
                        ncPercentElem.classList.add('text-success');
                        ncPercentElem.classList.remove('text-danger');
                        ncStatusElem.textContent = 'Tăng';
                    } else if (ncPercent < 0) {
                        ncPercentElem.textContent = `${ncPercent}% `;
                        ncPercentElem.classList.add('text-danger');
                        ncPercentElem.classList.remove('text-success');
                        ncStatusElem.textContent = 'Giảm';
                    } else {
                        ncPercentElem.textContent = '0% ';
                        ncPercentElem.classList.remove('text-success', 'text-danger');
                        ncStatusElem.textContent = 'Không đổi';
                    }
                    new ApexCharts(document.querySelector("#new-customers-report-per-week"), {
                        chart: {
                            type: 'bar',
                            height: 80,
                            sparkline: {
                                enabled: true
                            }
                        },
                        series: [{
                            name: 'Khách mới',
                            data: data.new_customers_last_7_days
                        }],
                        xaxis: {
                            categories: data.new_customers_last_7_days_labels
                        },
                        colors: ['#34c38f'],
                        tooltip: {
                            enabled: true,
                            y: {
                                formatter: val => val + " KH"
                            }
                        }
                    }).render();

                    // Tổng số sản phẩm đã bán hôm nay
                    document.getElementById('products-sold-today').textContent = data.total_products_sold_today;
                    document.getElementById('products-sold-yesterday').textContent = data
                        .total_products_sold_yesterday;
                    const psPercentElem = document.getElementById('products-sold-percent');
                    const psStatusElem = document.getElementById('products-sold-status');
                    const psPercent = data.total_products_sold_percent;
                    if (psPercent > 0) {
                        psPercentElem.textContent = `+${psPercent}% `;
                        psPercentElem.classList.add('text-success');
                        psPercentElem.classList.remove('text-danger');
                        psStatusElem.textContent = 'Tăng';
                    } else if (psPercent < 0) {
                        psPercentElem.textContent = `${psPercent}% `;
                        psPercentElem.classList.add('text-danger');
                        psPercentElem.classList.remove('text-success');
                        psStatusElem.textContent = 'Giảm';
                    } else {
                        psPercentElem.textContent = '0% ';
                        psPercentElem.classList.remove('text-success', 'text-danger');
                        psStatusElem.textContent = 'Không đổi';
                    }
                    new ApexCharts(document.querySelector("#products-sold-report-per-week"), {
                        chart: {
                            type: 'bar',
                            height: 80,
                            sparkline: {
                                enabled: true
                            }
                        },
                        series: [{
                            name: 'Sản phẩm',
                            data: data.total_products_sold_last_7_days
                        }],
                        xaxis: {
                            categories: data.total_products_sold_last_7_days_labels
                        },
                        colors: ['#556ee6'],
                        tooltip: {
                            enabled: true,
                            y: {
                                formatter: val => val + " SP"
                            }
                        }
                    }).render();






                });
        });

        function renderRepeatCustomerChart(data) {
            new ApexCharts(document.querySelector("#repeat-customer-per-week"), {
                chart: {
                    type: 'line',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                        name: 'New Customer',
                        data: data.repeat_customer_new
                    },
                    {
                        name: 'Old Customer',
                        data: data.repeat_customer_old
                    }
                ],
                xaxis: {
                    categories: data.repeat_customer_labels
                },
                colors: ['#3b82f6', '#34d399'],
                stroke: {
                    width: 3
                },
                markers: {
                    size: 4
                },
                tooltip: {
                    y: {
                        formatter: val => val + " đơn"
                    }
                }
            }).render();
        }

        function fetchRepeatCustomerRate(range = 'day') {
            fetch(`/admin/dashboard/repeat-customer-rate?range=${range}`)
                .then(res => res.json())
                .then(data => {
                    document.querySelector("#repeat-customer-per-week").innerHTML = ""; // clear old chart
                    renderRepeatCustomerChart(data);
                });
        }

        document.getElementById('repeat-range').addEventListener('change', function() {
            fetchRepeatCustomerRate(this.value);
        });

        // Khi load trang, mặc định là 10 ngày gần nhất
        fetchRepeatCustomerRate('day');

        function renderTopSellingProducts(products) {
            const list = document.getElementById('top-selling-products');
            list.innerHTML = '';
            products.forEach(product => {
                list.innerHTML += `
        <li class="list-group-item align-items-center d-flex justify-content-between">
            <div class="product-list">
                <img class="avatar-md p-1 rounded-circle bg-primary-subtle img-fluid me-3"
                    src="${product.image ? window.location.origin + '/storage/' + product.image : 'https://via.placeholder.com/56x56?text=No+Image'}"
                    >
                <div class="product-body align-self-center">
                    <h6 class="m-0 fw-semibold">${product.product_name}</h6>
                    <p class="mb-0 mt-1 text-muted">SKU: ${product.product_sku ?? ''}</p>
                    <p class="mb-0 mt-1 text-muted">${product.product_attribute ?? ''}</p>
                </div>
            </div>
            <div class="product-price text-end">
                <h6 class="m-0 fw-semibold">${Number(product.product_price).toLocaleString()} đ</h6>
                <p class="mb-0 mt-1 text-muted">${product.sold ?? 0} Sold</p>
            </div>
        </li>
        `;
            });
        }

        fetch('/admin/dashboard/top-selling-products')
            .then(res => res.json())
            .then(data => renderTopSellingProducts(data));

        fetch('/admin/dashboard/sales-report-income')
            .then(res => res.json())
            .then(data => {
                new ApexCharts(document.querySelector("#sale-report"), {
                    chart: {
                        type: 'bar',
                        height: 350,
                        toolbar: {
                            show: false
                        }
                    },
                    series: [{
                        name: 'Income',
                        data: data.income
                    }],
                    xaxis: {
                        categories: data.labels
                    },
                    colors: ['#556ee6'],
                    tooltip: {
                        y: {
                            formatter: val => val.toLocaleString() + " đ"
                        }
                    }
                }).render();
            });
    </script>
@endpush
