<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function createPaymentUrl(Order $order)
    {
        $vnp_Url = config('services.vnpay.url');
        $vnp_Returnurl = config('services.vnpay.return_url');
        $vnp_TmnCode = config('services.vnpay.tmn_code');
        $vnp_HashSecret = config('services.vnpay.hash_secret');

        $vnp_TxnRef = $order->sku;
        $vnp_OrderInfo = 'Thanh toan don hang: ' . $vnp_TxnRef;
        $vnp_Amount = (int)($order->total_amount * 100);
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();
        $vnp_OrderType = 'billpayment';

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => now()->format('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        ksort($inputData);

        // Tạo chuỗi hashData dùng http_build_query (space = +)
        $hashData = http_build_query($inputData);

        $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // Thêm chữ ký vào data
        $inputData["vnp_SecureHash"] = $vnp_SecureHash;

        // Build URL thanh toán
        $vnp_Url .= '?' . http_build_query($inputData);

        Log::info('VNPAY URL: ' . $vnp_Url);
        Log::info('VNPAY Secure Hash: ' . $vnp_SecureHash);
        Log::info('hash data: ' . $hashData);

        return $vnp_Url;
    }



    public function vnpayReturn(Request $request)
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret');
        $inputData = $request->all();

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);
        $hashData = http_build_query($inputData);
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        Log::info('Returned hashData: ' . $hashData);
        Log::info('Returned secureHash: ' . $secureHash);
        Log::info('Returned vnp_SecureHash: ' . $vnp_SecureHash);

        if ($secureHash === $vnp_SecureHash) {
            $orderId = $inputData['vnp_TxnRef'] ?? null; // Mã đơn hàng
            $responseCode = $inputData['vnp_ResponseCode'] ?? '';

            $order = Order::with('items')->where('sku', $orderId)->first();

            if ($order) {
                if ($responseCode == '00') {
                    // Thanh toán thành công
                    $order->payment_status = 'paid';
                    $order->save();

                    return view('client.payment.success', ['data' => $inputData]);
                } else {
                    // Thanh toán thất bại
                    $order->payment_status = 'failed';
                    $order->order_status = 'Hủy đơn';
                    $order->cancel_reason = 'Thanh toán không thành công';
                    $order->save();

                    // Hoàn lại tồn kho sản phẩm
                    foreach ($order->items as $item) {
                        if ($item->product_variant_sku) {
                            // Tìm sản phẩm theo SKU
                            $variant = \App\Models\ProductVariant::where('sku', $item->product_variant_sku)->first();
                            if ($variant) {
                                $variant->quantity += $item->quantity;
                                $variant->save();
                            }
                        }
                    }

                    return view('client.payment.failed', ['data' => $inputData]);
                }
            } else {
                return "Không tìm thấy đơn hàng.";
            }
        } else {
            return "Chữ ký không hợp lệ!";
        }
    }
}
