@extends('layouts.admin')

@section('title', 'Chi tiết phương thức thanh toán - ' . $paymentMethod->name)

@section('content')
    <style>
        .payment-method-container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
        }

        .status-active {
            color: #28a745;
            font-weight: bold;
        }

        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }
    </style>

    <div class="container payment-method-container">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.paymentMethods.index') }}">Phương thức thanh toán</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $paymentMethod->name }}</li>
            </ol>
        </nav>

        <h2 class="mb-4">Chi tiết phương thức: {{ $paymentMethod->name }}</h2>

        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <span class="detail-label">Tên phương thức:</span> {{ $paymentMethod->name }}
                </div>

                <div class="mb-3">
                    <span class="detail-label">Mô tả:</span><br>
                    {!! $paymentMethod->description ?? '<span class="text-muted">Không có mô tả</span>' !!}
                </div>

                <div class="mb-3">
                    <span class="detail-label">Trạng thái:</span>
                    <span class="{{ $paymentMethod->status ? 'status-active' : 'status-inactive' }}">
                        {{ $paymentMethod->status ? 'Kích hoạt' : 'Tạm tắt' }}
                    </span>
                </div>

                <div class="mb-3">
                    <span class="detail-label">Ngày tạo:</span>
                    {{ $paymentMethod->created_at ? $paymentMethod->created_at->format('d/m/Y H:i') : 'N/A' }}
                </div>

                <div class="mb-3">
                    <span class="detail-label">Ngày cập nhật:</span>
                    {{ $paymentMethod->updated_at ? $paymentMethod->updated_at->format('d/m/Y H:i') : 'N/A' }}
                </div>

                <div class="mt-4">
                    <a href="{{ route('admin.paymentMethods.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
