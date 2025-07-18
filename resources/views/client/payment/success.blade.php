@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>✅ Thanh toán thành công</h2>
        <p>Mã đơn hàng: {{ $data['vnp_TxnRef'] }}</p>
        <p>Số tiền: {{ number_format($data['vnp_Amount'] / 100, 0, ',', '.') }} VND</p>
        <p>Thời gian: {{ $data['vnp_PayDate'] }}</p>
    </div>
@endsection
