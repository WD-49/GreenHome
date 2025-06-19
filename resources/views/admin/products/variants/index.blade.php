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



                    <div id="variant-table-wrapper">
                        <x-table-wrapper :url="route('admin.products.variants.index', $product)">
                            @include('admin.products.variants.partials.table', ['variants' => $variants])
                        </x-table-wrapper>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
