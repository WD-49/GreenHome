<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Mail\OrderInvoiceMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Notifications\OrderStatusNotification;
use App\Notifications\OrderPaymentNotification;

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

        Log::info('URL thanh toán VNPAY: ' . $vnp_Url);
        Log::info('Chữ ký bảo mật VNPAY: ' . $vnp_SecureHash);
        Log::info('Dữ liệu hash: ' . $hashData);

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

        Log::info('Dữ liệu hash trả về: ' . $hashData);
        Log::info('Chữ ký bảo mật trả về: ' . $secureHash);
        Log::info('Chữ ký VNPAY nhận được: ' . $vnp_SecureHash);

        if ($secureHash === $vnp_SecureHash) {
            $orderId = $inputData['vnp_TxnRef'] ?? null; // Mã đơn hàng
            $responseCode = $inputData['vnp_ResponseCode'] ?? '';

            $order = Order::with(['items', 'user.profile'])->where('sku', $orderId)->first();

            if ($order) {
                if ($responseCode == '00') {
                    // Thanh toán thành công
                    $order->order_status = 'Xác nhận';
                    $order->payment_status = 'paid';
                    $order->save();

                    // Gửi thông báo thanh toán thành công
                    try {
                        $order->user->notify(new OrderPaymentNotification($order, 'paid'));
                        Log::info('Gửi thông báo thanh toán thành công', [
                            'order_id' => $order->id,
                            'sku' => $order->sku,
                            'user_id' => $order->user->id,
                            'trạng_thái' => 'thành công',
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Lỗi khi gửi thông báo thanh toán', [
                            'order_id' => $order->id,
                            'lỗi' => $e->getMessage(),
                        ]);
                    }

                    // Gửi email hóa đơn
                    try {
                        if ($order->user) {
                            Log::info('Đã xếp hàng gửi email hóa đơn', [
                                'order_id' => $order->id,
                                'sku' => $order->sku,
                                'user_id' => $order->user->id,
                                'user_profile' => $order->user->profile,
                            ]);
                        } else {
                            Log::error('Không tìm thấy người dùng cho đơn hàng', [
                                'order_id' => $order->id,
                                'sku' => $order->sku,
                                'user_id' => $order->user_id ?? 'không có',
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Lỗi khi xếp hàng gửi email hóa đơn', [
                            'order_id' => $order->id,
                            'lỗi' => $e->getMessage(),
                        ]);
                    }
                    return view('client.payment.success', ['data' => $inputData]);
                } else {
                    // Thanh toán thất bại
                    $order->payment_status = 'pending';
                    $order->order_status = 'Xác nhận';
                    $order->save();

                    // Gửi thông báo nhắc nhở thanh toán
                    try {
                        $order->user->notify(new OrderPaymentNotification($order, 'fail'));
                        Log::info('Gửi thông báo thanh toán thất bại', [
                            'order_id' => $order->id,
                            'sku' => $order->sku,
                            'user_id' => $order->user->id,
                            'trạng_thái' => 'thất bại',
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Lỗi khi gửi thông báo thanh toán', [
                            'order_id' => $order->id,
                            'lỗi' => $e->getMessage(),
                        ]);
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

    public function payAgain(Order $order)
    {
        // Chỉ cho thanh toán lại nếu đủ điều kiện
        if (! $order->canBePay()) {
            return redirect()->back()->with('error', 'Đơn hàng này không thể thanh toán lại.');
        }

        // Tạo lại URL thanh toán VNPAY
        $redirectUrl = $this->createPaymentUrl($order);

        return redirect()->away($redirectUrl);
    }
}
