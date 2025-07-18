@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>❌ Thanh toán thất bại</h2>
        <p>Mã lỗi: {{ $data['vnp_ResponseCode'] ?? 'Không xác định' }}</p>
    </div>
@endsection
