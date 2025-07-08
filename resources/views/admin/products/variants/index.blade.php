{{-- filepath: c:\laragon\www\GreenHome\resources\views\admin\products\variants\index.blade.php --}}
@extends('layouts.admin')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="py-3 d-flex align-items-center flex-sm-row flex-column mb-3">
        <div class="flex-grow-1 d-flex align-items-center gap-2">
            <i class="mdi mdi-cube-outline fs-3 text-primary"></i>
            <h4 class="fs-20 fw-bold m-0">{{ $title }}</h4>
        </div>
        <div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                Quay lại danh sách sản phẩm
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Danh sách biến thể sản phẩm</h5>
                    <div>
                        <a href="{{ route('admin.products.variants.create', $product) }}" class="btn btn-success shadow-sm">
                            + Thêm biến thể
                        </a>
                        <a href="{{ route('admin.products.variants.trashed', $product) }}" class="btn btn-danger shadow-sm">
                            <i class="fas fa-trash-restore fa-sm text-white-50"></i> Thùng rác
                            ({{ $variantTrashed->count() }})
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <div>
                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                                <i class="mdi mdi-filter-outline me-1"></i> Bộ lọc
                            </button>
                        </div>

                    </div>
                    <div class="collapse mb-3" id="filterCollapse">
                        <div class="card card-body">
                            <form id="filter-form" method="GET"
                                action="{{ route('admin.products.variants.index', $product) }}">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="sku" class="form-label">Mã sản phẩm</label>
                                        <input type="text" class="form-control" name="sku"
                                            value="{{ request('sku') }}">
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
                                    <div class="col-md-2">
                                        <label for="min_price" class="form-label">Giá từ</label>
                                        <input type="number" class="form-control" name="min_price"
                                            value="{{ request('min_price') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <label for="max_price" class="form-label">Giá đến</label>
                                        <input type="number" class="form-control" name="max_price"
                                            value="{{ request('max_price') }}">
                                    </div>
                                    <div class="col-md-12 text-end">
                                        <button type="submit" class="btn btn-outline-primary">Lọc</button>
                                        <button type="reset" class="btn btn-outline-primary">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div id="variant-table-wrapper">
                        <x-table-wrapper :url="route('admin.products.variants.index', $product)">
                            @include('admin.products.variants.partials.table', ['variants' => $variants])
                        </x-table-wrapper>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/app.js')
@endsection
