<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hóa đơn đơn hàng {{ $order->sku }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body
    style="font-family: Arial, sans-serif; font-size:14px; color:#333; background-color:#f9f9f9; margin:0; padding:20px;">

    <table width="100%" cellpadding="0" cellspacing="0"
        style="max-width: 600px; margin: 0 auto; background: #fff; border-collapse: collapse; border: 1px solid #e0e0e0;">
        <tr>
            <td style="padding: 20px; text-align: center;">
                <h2 style="margin: 0;">Thông tin đơn hàng</h2>
                <p style="margin: 5px 0;">Mã đơn: <strong>{{ $order->sku }}</strong></p>
            </td>
        </tr>

        <tr>
            <td style="padding: 20px;">
                <h3 style="margin-bottom: 5px;">Người đặt</h3>
                <p style="margin: 0;">{{ $user->name }}</p>
                <p style="margin: 0;">{{ $user->profile->address ?? '' }}</p>
                <p style="margin: 0;">{{ $user->email }} | {{ $user->profile->phone ?? '' }}</p>
            </td>
        </tr>

        <tr>
            <td style="padding: 20px;">
                <h3 style="margin-bottom: 5px;">Người nhận</h3>
                <p style="margin: 0;">{{ $order->shipping_name }}</p>
                <p style="margin: 0;">{{ $order->shipping_address }}</p>
                <p style="margin: 0;">{{ $order->shipping_phone }}</p>
            </td>
        </tr>

        <tr>
            <td style="padding: 20px;">
                <h3>Chi tiết đơn hàng</h3>
                <table width="100%" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f0f0f0;">
                            <th align="left" style="border: 1px solid #ddd;">#</th>
                            <th align="left" style="border: 1px solid #ddd;">Sản phẩm</th>
                            <th align="right" style="border: 1px solid #ddd;">Giá</th>
                            <th align="center" style="border: 1px solid #ddd;">SL</th>
                            <th align="right" style="border: 1px solid #ddd;">Tổng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $index => $item)
                            <tr>
                                <td style="border: 1px solid #ddd;">{{ $index + 1 }}</td>
                                <td style="border: 1px solid #ddd;">
                                    {{ $item->product_name }}
                                    @if (!empty($item->product_attribute))
                                        <br><small>(Loại: {{ $item->product_attribute }})</small>
                                    @endif
                                </td>
                                <td align="right" style="border: 1px solid #ddd;">
                                    {{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
                                <td align="center" style="border: 1px solid #ddd;">{{ $item->quantity }}</td>
                                <td align="right" style="border: 1px solid #ddd;">
                                    {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}đ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p style="margin-top: 20px;"><strong>Ghi chú:</strong> {{ $order->note ?? 'Không' }}</p>
            </td>
        </tr>

        <tr>
            <td style="padding: 20px;">
                <table width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td align="left"><strong>Tổng tiền sản phẩm:</strong></td>
                        <td align="right">
                            {{ number_format($order->items->sum(fn($i) => $i->unit_price * $i->quantity), 0, ',', '.') }}đ
                        </td>
                    </tr>
                    @if ($order->items->sum('discount_amount') > 0)
                        <tr>
                            <td align="left"><strong>Giảm giá sản phẩm:</strong></td>
                            <td align="right">
                                -{{ number_format($order->items->sum('discount_amount'), 0, ',', '.') }}đ</td>
                        </tr>
                    @endif
                    @if ($order->discount_amount > $order->items->sum('discount_amount'))
                        <tr>
                            <td align="left"><strong>Giảm giá toàn đơn:</strong></td>
                            <td align="right">
                                -{{ number_format($order->discount_amount - $order->items->sum('discount_amount'), 0, ',', '.') }}đ
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td align="left"><strong>Phí vận chuyển:</strong></td>
                        <td align="right">{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</td>
                    </tr>
                    <tr style="border-top: 2px solid #000;">
                        <td align="left"><strong>Tổng cộng:</strong></td>
                        <td align="right"><strong>{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td style="padding: 20px; text-align: center; font-size: 13px; color: #888;">
                Cảm ơn bạn đã mua hàng tại GreenHome!<br>
                Nếu có bất kỳ câu hỏi nào về đơn hàng, vui lòng liên hệ với chúng tôi qua email hoặc số điện thoại
                trên trang web.<br>
                <strong>Chúc bạn một ngày tốt lành!</strong>
            </td>
        </tr>
    </table>

</body>

</html>
