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
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="filter-from">Từ ngày</label>
                    <input type="date" id="filter-from" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="filter-to">Đến ngày</label>
                    <input type="date" id="filter-to" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button class="btn btn-success" id="apply-filter">Áp dụng</button>
                    <a href="{{ route('admin.dashboard') }}"><button class="btn btn-primary">làm
                            mới</button></a>
                </div>

            </div>

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
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="border border-dark rounded-2 me-2 widget-icons-sections">
                        <i data-feather="bar-chart" class="widgets-icons"></i>
                    </div>
                    <h5 class="card-title mb-0">Báo cáo doanh thu</h5>
                </div>
            </div>

            <div style="overflow-x:auto; white-space: nowrap;">
                <div id="sales-report-chart" class="apex-charts" style="min-width: 900px;"></div>
            </div>
        </div>



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
                        <h5 class="card-title mb-0">Sản phẩm bán chạy</h5>
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
                        <h5 class="card-title mb-0">Tỷ lệ khách hàng quay lại mua hàng</h5>
                    </div>

                </div>

                <div style="overflow-x: auto;">
                    <div id="repeat-customer-per-week" class="apex-charts" style="min-width: 900px;"></div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/ecommerce-dashboard.init.js') }}"></script>
    <script>
        const endpoint = '/admin/dashboard';
        let salesChart = null;
        let repeatCustomerChart = null; // global

        document.addEventListener('DOMContentLoaded', () => {
            const today = new Date();
            const todayStr = today.toISOString().slice(0, 10);

            // Tính ngày 10 ngày trước
            const priorDate = new Date();
            priorDate.setDate(today.getDate() - 10);
            const priorDateStr = priorDate.toISOString().slice(0, 10);

            document.getElementById('filter-from').value = priorDateStr;
            document.getElementById('filter-to').value = todayStr;

            // Load mặc định khi mở dashboard
            loadDashboard(priorDateStr, todayStr);
            fetchTopSellingProducts(priorDateStr, todayStr);

            // Gắn sự kiện
            document.getElementById('apply-filter').addEventListener('click', () => {
                const from = document.getElementById('filter-from').value;
                const to = document.getElementById('filter-to').value;

                if (!from || !to) {
                    return alert('Vui lòng chọn đầy đủ Từ ngày và Đến ngày');
                }
                if (from > to) {
                    return alert('Từ ngày không được lớn hơn Đến ngày');
                }

                loadDashboard(from, to);
                fetchTopSellingProducts(from, to);
            });

            // Top selling products luôn không theo ngày
            fetch(`${endpoint}/top-selling-products?from=${priorDateStr}&to=${todayStr}`)
                .then(res => res.json())
                .then(renderTopSellingProducts);
        });

        function loadDashboard(from, to) {
            loadDashboardData(from, to);
            fetchRepeatCustomerRate('day', from, to);
            const year = new Date(to).getFullYear();
            renderSalesReport(year, from, to);
        }

        function loadDashboardData(from, to) {
            let url = `${endpoint}/data?from=${from}&to=${to}`;
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    updateStatCard('orders', data.orders_today, data.orders_yesterday, data.orders_percent_change, data
                        .orders_last_7_days, data.orders_last_7_days_labels, '#556ee6', 'đơn');
                    updateStatCard('sales', data.sales_today, data.sales_yesterday, data.sales_percent_change, data
                        .sales_last_7_days, data.sales_last_7_days_labels, '#f46a6a', 'đ');
                    updateStatCard('new-customers', data.new_customers_today, data.new_customers_yesterday, data
                        .new_customers_percent, data.new_customers_last_7_days, data
                        .new_customers_last_7_days_labels, '#34c38f', 'KH');
                    updateStatCard('products-sold', data.total_products_sold_today, data.total_products_sold_yesterday,
                        data.total_products_sold_percent, data.total_products_sold_last_7_days, data
                        .total_products_sold_last_7_days_labels, '#556ee6', 'SP');
                });
        }



        function fetchRepeatCustomerRate(range = 'day', from = '', to = '') {
            let url = `${endpoint}/repeat-customer-rate?range=${range}`;
            if (from && to) {
                url += `&from=${from}&to=${to}`;
            }

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (repeatCustomerChart) {
                        repeatCustomerChart.destroy();
                    }

                    repeatCustomerChart = new ApexCharts(document.querySelector("#repeat-customer-per-week"), {
                        chart: {
                            type: 'line',
                            height: 300,
                            width: Math.max(900, data.repeat_customer_labels.length * 100),
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: true,
                                type: 'x',
                                autoScaleYaxis: true
                            },
                            animations: {
                                enabled: true
                            },
                        },
                        series: [{
                                name: 'New Customer',
                                data: data.repeat_customer_new || []
                            },
                            {
                                name: 'Old Customer',
                                data: data.repeat_customer_old || []
                            }
                        ],
                        xaxis: {
                            categories: data.repeat_customer_labels || []
                        },
                        colors: ['#3b82f6', '#34d399'],
                        stroke: {
                            width: 3
                        },
                        markers: {
                            size: 4
                        },
                        tooltip: {
                            enabled: true,
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: val => `${val} đơn`
                            }
                        }
                    });


                    repeatCustomerChart.render();
                });
        }


        function renderSalesReport(year, from = '', to = '', filterIncome = false) {
            let url = `${endpoint}/sales-report-income?year=${year}`;
            if (from && to) {
                url += `&from=${from}&to=${to}`;
            }
            if (filterIncome) {
                url += '&filter_income=1';
            }

            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (salesChart) salesChart.destroy();

                    salesChart = new ApexCharts(document.querySelector("#sales-report-chart"), {
                        chart: {
                            type: 'bar',
                            height: 350,
                            width: Math.max(900, data.labels.length *
                                150), // chiều rộng tùy theo số ngày, mỗi ngày 50px
                            toolbar: {
                                show: false
                            },
                            zoom: {
                                enabled: true,
                                type: 'x',
                                autoScaleYaxis: true,
                            },
                            animations: {
                                enabled: true
                            },
                        },
                        series: [{
                            name: 'Doanh thu',
                            data: data.income
                        }],
                        xaxis: {
                            categories: data.labels,
                            title: {
                                text: 'Ngày',
                                style: {
                                    fontWeight: 600
                                }
                            },
                            labels: {
                                rotate: -45
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'VNĐ',
                                style: {
                                    fontWeight: 600
                                }
                            },
                            labels: {
                                formatter: val => `${val.toLocaleString()} đ`
                            }
                        },
                        colors: ['#3b82f6'],
                        tooltip: {
                            y: {
                                formatter: val => `${val.toLocaleString()} đ`
                            }
                        },
                        plotOptions: {
                            bar: {
                                columnWidth: '40%',
                                distributed: false,
                                borderRadius: 4
                            }
                        },
                        dataLabels: {
                            enabled: false
                        }
                    });


                    salesChart.render();
                });
        }




        function updateStatCard(idPrefix, today, yesterday, percent, series, labels, color, suffix) {
            const todayEl = document.getElementById(`${idPrefix}-today`);
            const yesterdayEl = document.getElementById(`${idPrefix}-yesterday`);
            const percentEl = document.getElementById(`${idPrefix}-percent`);
            const statusEl = document.getElementById(`${idPrefix}-status`);

            todayEl.textContent = Number(today).toLocaleString();
            yesterdayEl.textContent = Number(yesterday).toLocaleString();

            let statusText = 'Không đổi';
            percentEl.classList.remove('text-success', 'text-danger');

            if (percent > 0) {
                statusText = 'Tăng';
                percentEl.classList.add('text-success');
                percentEl.textContent = `+${percent}%`;
            } else if (percent < 0) {
                statusText = 'Giảm';
                percentEl.classList.add('text-danger');
                percentEl.textContent = `${percent}%`;
            } else {
                percentEl.textContent = '0%';
            }

            statusEl.textContent = statusText;

            const chartEl = document.getElementById(`${idPrefix}-report-per-week`);
            chartEl.innerHTML = '';
            new ApexCharts(chartEl, {
                chart: {
                    type: 'bar',
                    height: 80,
                    sparkline: {
                        enabled: true
                    }
                },
                series: [{
                    name: idPrefix,
                    data: series
                }],
                xaxis: {
                    categories: labels
                },
                colors: [color],
                tooltip: {
                    enabled: true,
                    y: {
                        formatter: val => `${Number(val).toLocaleString()} ${suffix}`
                    }
                }
            }).render();
        }

        function fetchTopSellingProducts(from = '', to = '') {
            let url = `${endpoint}/top-selling-products`;
            if (from && to) {
                url += `?from=${from}&to=${to}`;
            }

            fetch(url)
                .then(res => res.json())
                .then(renderTopSellingProducts);
        }

        function renderTopSellingProducts(products) {
            const list = document.getElementById('top-selling-products');
            list.innerHTML = '';
            products.forEach(product => {
                const imgSrc = product.image ? `${window.location.origin}/storage/${product.image}` :
                    '/storage/default.png';
                list.innerHTML += `
            <li class="list-group-item align-items-center d-flex justify-content-between">
                <div class="product-list">
                    <img class="avatar-md p-1 rounded-circle bg-primary-subtle img-fluid me-3" src="${imgSrc}">
                    <div class="product-body align-self-center">
                        <h6 class="m-0 fw-semibold">${product.product_name}</h6>
                        <p class="mb-0 mt-1 text-muted">${product.product_attribute ?? ''}</p>
                    </div>
                </div>
                <div class="product-price text-end">
                    <h6 class="m-0 fw-semibold">${Number(product.product_price).toLocaleString()} đ</h6>
                    <p class="mb-0 mt-1 text-muted">${product.sold ?? 0} Sold</p>
                </div>
            </li>`;
            });
        }
    </script>
@endpush
