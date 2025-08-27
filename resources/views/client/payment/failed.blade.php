@extends('layouts.app')

@section('content')
    <div class="container py-5 text-center">
        <div class="alert alert-danger p-4 rounded-4 shadow-sm">
            <h2 class="mb-3"><i class="bi bi-x-circle-fill text-danger"></i> Thanh toán thất bại</h2>
            <p class="mb-1"><strong>Mã lỗi:</strong> {{ $data['vnp_ResponseCode'] ?? 'Không xác định' }}</p>
            <p class="mb-1">Giao dịch không thành công. Vui lòng thử lại hoặc chọn phương thức khác.</p>
            <a href="{{ route('home') }}" class="btn btn-danger mt-4">Quay lại Trang trủ</a>
        </div>
    </div>
@endsection
