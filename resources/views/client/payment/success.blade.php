@extends('layouts.app')

@section('content')
    <div class="container py-5 text-center">
        <div class="alert alert-success p-4 rounded-4 shadow-sm">
            <h2 class="mb-3"><i class="bi bi-check-circle-fill text-success"></i> Thanh toán thành công!</h2>
            <p class="mb-1"><strong>Mã đơn hàng:</strong> {{ $data['vnp_TxnRef'] }}</p>
            <p class="mb-1"><strong>Số tiền:</strong> {{ number_format($data['vnp_Amount'] / 100, 0, ',', '.') }} VND</p>
            <p class="mb-1"><strong>Thời gian thanh toán:</strong>
                {{ \Carbon\Carbon::parse($data['vnp_PayDate'])->format('H:i:s d/m/Y') }}</p>
            <a href="{{ route('home') }}" class="btn btn-success mt-4">Về trang chủ</a>
        </div>
    </div>
@endsection
