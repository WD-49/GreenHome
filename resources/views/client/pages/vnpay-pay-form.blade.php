<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thanh toán với VNPAY</title>
</head>

<body>
    <h2>Thanh toán đơn hàng</h2>
    <form action="{{ route('vnpay.pay') }}" method="get">
        <button type="submit">Thanh toán 10.000đ bằng VNPAY</button>
    </form>
</body>

</html>
