@extends('layouts.admin')

@section('title')
    {{ $title }}
@endsection


@section('content')
    <div class="py-3 d-flex align-items-center flex-sm-row flex-column mb-3">
        <div class="flex-grow-1 d-flex align-items-center gap-2">
            <i class="mdi mdi-cube-outline fs-3 text-primary"></i>
            <h4 class="fs-20 fw-bold m-0">Quản lý sản phẩm</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Danh sách sản phẩm</h5>
                    <div>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-success shadow-sm">
                            + Thêm sản phẩm
                        </a>
                        <a href="{{ route('admin.products.trashed') }}" class="btn btn-danger shadow-sm">
                            <i class="fas fa-trash-restore fa-sm text-white-50"></i> Thùng rác
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form id="perPageForm" method="GET" action="{{ route('admin.products.index') }}"
                            class="d-flex align-items-center">
                            <label for="perPage" class="me-2 mb-0">Show</label>
                            <select name="per_page" id="perPage" class="form-select form-select-sm w-auto"
                                onsubmit="return false;">
                                @foreach ([10, 20, 50, 100] as $size)
                                    <option value="{{ $size }}"
                                        {{ request('per_page', 10) == $size ? 'selected' : '' }}>{{ $size }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="ms-2">entries</span>
                            @foreach (request()->except('per_page', 'page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        </form>
                        <div>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                                <i class="mdi mdi-filter-outline me-1"></i> Bộ lọc
                            </button>
                        </div>

                    </div>
                    <div class="collapse mb-3" id="filterCollapse">
                        <div class="card card-body">
                            <form id="filter-form" method="GET" action="{{ route('admin.products.index') }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="name" class="form-label">Tên sản phẩm</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ request('name') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="category" class="form-label">Danh mục</label>
                                        <select class="form-select" name="category_id">
                                            <option value="">Tất cả</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="status" class="form-label">Trạng thái</label>
                                        <select class="form-select" name="status">
                                            <option value="">Tất cả</option>
                                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                                                Đang
                                                bán</option>
                                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                                                Dừng
                                                bán</option>
                                        </select>
                                    </div>
                                    <!-- Thêm bộ lọc theo ngày -->
                                    <div class="col-md-2">
                                        <label for="min_date" class="form-label">Ngày từ</label>
                                        <input type="date" class="form-control" name="min_date"
                                            value="{{ request('min_date') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="max_date" class="form-label">Ngày đến</label>
                                        <input type="date" class="form-control" name="max_date"
                                            value="{{ request('max_date') }}">
                                    </div>

                                    <div class="col-md-12 text-end">
                                        <button type="submit" class="btn btn-outline-primary">Lọc</button>
                                        <button type="reset" class="btn btn-outline-primary">Reset</button>
                                        {{-- <a href="{{ route('admin.products.index') }}"
                                            class="btn btn-outline-secondary">Reset</a> --}}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div id="product-table-wrapper">
                        <x-table-wrapper :url="route('admin.products.index')">
                            @include('admin.products.table', ['products' => $products])
                        </x-table-wrapper>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/app.js')
@endsection
