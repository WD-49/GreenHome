<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\Discount;
use App\Models\DiscountUsage;
use App\Models\ProductVariant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelVnpayOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $orderId;

    public $tries = 3; // Thử lại 3 lần nếu thất bại
    public $timeout = 120; // Timeout sau 120 giây

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        Log::debug('CancelVnpayOrderJob running for order ID: ' . $this->orderId . ' at ' . now()->toDateTimeString());

        DB::transaction(function () {
            $order = Order::with('items')
                ->where('id', $this->orderId)
                ->where('payment_method_name', 'VNPAY')
                ->where('payment_status', 'pending')
                ->first();

            if (!$order) {
                Log::info('Order ID: ' . $this->orderId . ' not found or already processed');
                return;
            }

            // Hoàn tồn kho
            foreach ($order->items as $item) {
                if ($item->product_variant_sku) {
                    $variant = ProductVariant::where('sku', $item->product_variant_sku)->first();
                    if ($variant) {
                        $variant->quantity += $item->quantity;
                        $variant->save();
                        Log::info("Restocked {$item->quantity} for SKU: {$variant->sku}");
                    }
                }
            }

            // Hoàn mã giảm giá
            if (!empty($order->discount_code)) {
                $discount = Discount::where('code', $order->discount_code)->first();
                if ($discount) {
                    $discount->quantity += 1;
                    $discount->save();
                    Log::info("Restored 1 discount code: {$discount->code}");
                }
            }

            // Xóa discount usage
            DiscountUsage::where('order_id', $order->id)->delete();

            // Cập nhật trạng thái đơn hàng
            $order->order_status = 'Hủy đơn';
            $order->cancel_reason = 'Đơn hàng đã bị hủy do quá hạn thanh toán online, bạn cần thanh toán trong vòng 24h kể từ khi đặt hàng.';
            $order->payment_status = 'failed';
            $order->updated_at = now();
            $order->save();

            // Gửi email thông báo (tùy chọn)
            // if ($order->user && $order->user->email) {
            //     Mail::to($order->user->email)->send(new OrderCancelled($order));
            // }

            Log::info("Cancelled order: {$order->sku}");
        });
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CancelVnpayOrderJob failed for order ID: ' . $this->orderId . ', Error: ' . $exception->getMessage());
    }
}
