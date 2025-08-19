<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Discount;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DiscountUsage;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use App\Models\DiscountProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Notifications\OrderStatusNotification;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Lấy danh sách các trạng thái đơn hàng ENUM (có thể dùng chung).
     * @return array
     */
    protected function getOrderEnumStatuses()
    {
        // Các giá trị trạng thái đơn hàng
        return ['Chưa xác nhận', 'Xác nhận', 'Đang vận chuyển', 'Giao hàng thành công', 'Hủy đơn', 'Đã nhận hàng'];
    }

    /**
     * Lấy danh sách các trạng thái thanh toán ENUM (có thể dùng chung).
     * @return array
     */
    protected function getPaymentEnumStatuses()
    {
        // Các giá trị trạng thái thanh toán
        return ['pending', 'paid', 'failed'];
    }

    /**
     * Map trạng thái thanh toán tiếng Anh sang tiếng Việt.
     * @param string|null $status
     * @return string
     */
    protected function mapPaymentStatusToVietnamese(?string $status)
    {
        return [
            'pending' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thất bại',
        ][$status] ?? 'Không xác định';
    }


    public function index(Request $request)
    {
        $query = Order::with([
            'user',
            'items',
        ])->latest();

        // Lọc theo mã đơn hàng (sku hoặc id)
        if ($request->filled('order_code')) {
            $code = $request->order_code;
            $query->where(function ($q) use ($code) {
                $q->where('sku', 'like', "%{$code}%")
                    ->orWhere('id', $code);
            });
        }

        // Lọc theo tên khách hàng
        if ($request->filled('customer_name')) {
            $name = $request->customer_name;
            $query->whereHas('user', function ($q) use ($name) {
                $q->where('name', 'like', "%{$name}%");
            });
        }

        // Lọc theo trạng thái thanh toán (payment_status: pending, paid, failed)
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Lọc theo trạng thái đơn hàng (order_status: enum)
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->order_status);
        }

        // Lọc theo ngày đặt (ngày bắt đầu và ngày kết thúc)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Lọc theo phương thức thanh toán (payment_method_id)
        if ($request->filled('payment_method')) {
            $query->where('payment_method_id', $request->payment_method);
        }

        // Đếm số lượng đơn hàng chưa xác nhận hôm nay
        $unconfirmedTodayCount = Order::where('order_status', 'Chưa xác nhận')
            ->whereDate('created_at', now())
            ->count();

        // Lấy kết quả phân trang, bao gồm cả những đơn hàng đã xóa mềm (nếu cần hiển thị)
        // $orders = $query->withTrashed()->paginate(20)->withQueryString();
        $orders = $query->withTrashed()->get();

        // dd($orders);

        $orderStatuses = $this->getOrderEnumStatuses();
        $paymentMethods = PaymentMethod::all();
        $paymentStatuses = $this->getPaymentEnumStatuses();

        return view('admin.orders.index', compact('orders', 'orderStatuses', 'paymentMethods', 'paymentStatuses', 'unconfirmedTodayCount'));
    }

    public function show($id)
    {
        $order = Order::with([
            'user.profile',
            'items' => function ($query) {
                $query->withTrashed();
            },

        ])
            ->withTrashed()
            ->findOrFail($id);

        $discountProductIds = $order->discount?->products->pluck('id')->toArray() ?? [];

        $allOrderStatuses = $this->getOrderEnumStatuses();
        $paymentStatuses = $this->getPaymentEnumStatuses();
        // dd($order);

        return view('admin.orders.show', compact('order', 'discountProductIds', 'allOrderStatuses', 'paymentStatuses'));
    }


    public function updateStatus(Request $request, $id)
    {
        // Log::info('--- Order Update Status Attempt ---');
        // Log::info('Order ID: ' . $id);
        // Log::info('Request Data: ', $request->all());

        $order = Order::findOrFail($id);
        $newOrderStatus = $request->input('order_status');
        $oldOrderStatus = $order->order_status;

        // Log::info("Old Order Status: '{$oldOrderStatus}'");
        // Log::info("New Order Status from Request: '{$newOrderStatus}'");
        // Log::info("Cancel Reason from Request: " . $request->input('cancel_reason'));

        $rules = [
            'order_status' => 'required|in:' . implode(',', $this->getOrderEnumStatuses()),
        ];

        $messages = [
            'order_status.required' => 'Trạng thái đơn hàng không được để trống.',
            'order_status.in' => 'Trạng thái đơn hàng không hợp lệ.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $orderStatusesEnum = $this->getOrderEnumStatuses();
        $currentStatusIndex = array_search($oldOrderStatus, $orderStatusesEnum);
        $newStatusIndex = array_search($newOrderStatus, $orderStatusesEnum);

        $progressingStatuses = ['Chưa xác nhận', 'Xác nhận', 'Đang vận chuyển', 'Giao hàng thành công', 'Đã nhận hàng'];

        if (in_array($newOrderStatus, $progressingStatuses) && $newStatusIndex < $currentStatusIndex) {
            $validator->after(function ($validator) use ($oldOrderStatus, $newOrderStatus) {
                $validator->errors()->add('order_status', "Không thể chuyển từ '{$oldOrderStatus}' về trạng thái '{$newOrderStatus}' (trạng thái lùi).");
            });
        } elseif ($newOrderStatus === 'Hủy đơn') {
            if (!$order->canBeCancelled()) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('order_status', "Đơn hàng này không thể chuyển sang trạng thái 'Hủy đơn' từ trạng thái hiện tại.");
                });
            }
            if ($newOrderStatus === 'Hủy đơn' && empty($request->input('cancel_reason'))) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('cancel_reason', 'Vui lòng cung cấp lý do hủy nếu chọn trạng thái "Hủy đơn".');
                });
            }
        }
        if ($oldOrderStatus === 'Hủy đơn' && $newOrderStatus !== 'Hủy đơn') {
            // Phần này để xử lý việc xóa lý do hủy nếu trạng thái không còn là 'Hủy đơn'
        }

        if ($validator->fails()) {
            // Khi không dùng AJAX, trả về redirect với lỗi
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $order->order_status = $newOrderStatus;
            if ($newOrderStatus === 'Hủy đơn') {
                $order->cancel_reason = $request->input('cancel_reason');
            } else {
                $order->cancel_reason = null;
            }
            $order->save();
            $user = $order->user; // Giả sử Order có quan hệ với User
            if ($user) {
                try {
                    $user->notify(new OrderStatusNotification($order, $newOrderStatus));
                    Log::info('Order status notification sent/updated', [
                        'order_id' => $order->id,
                        'status' => $newOrderStatus,
                        'user_id' => $user->id,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send/update order status notification', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            Log::info('Order status updated successfully in DB.');

            // Trả về redirect thay vì JSON
            return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
        } catch (\Exception $e) {
            Log::error("Lỗi khi cập nhật trạng thái đơn hàng {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật trạng thái đơn hàng.');
        }
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'payment_status' => 'required|in:' . implode(',', $this->getPaymentEnumStatuses()),
        ], [
            'payment_status.required' => 'Trạng thái thanh toán không được để trống.',
            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ.',
        ]);

        if ($validator->fails()) {
            // Khi không dùng AJAX, trả về redirect với lỗi
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $order->payment_status = $request->payment_status;
            $order->save();

            // Trả về redirect thay vì JSON
            return redirect()->back()->with('success', 'Cập nhật trạng thái thanh toán thành công!');
        } catch (\Exception $e) {
            Log::error("Lỗi khi cập nhật trạng thái thanh toán đơn hàng {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật trạng thái thanh toán.');
        }
    }

    public function cancel(Request $request, Order $order)
    {
        Log::info('--- Bắt đầu xử lý hủy đơn hàng ---');
        Log::info('Order ID: ' . $order->id);
        Log::info('Trạng thái hiện tại của đơn hàng: ' . $order->order_status);
        Log::info('Request data (cancel): ', $request->all());

        try {
            // SỬA Ở ĐÂY: Đảm bảo chỉ có 'required' và 'max:1000'
            $validatedData = $request->validate([
                'cancel_reason' => 'required|string|max:1000', // Không có min:10 ở đây
            ], [
                'cancel_reason.required' => 'Vui lòng nhập lý do hủy đơn hàng.',
                'cancel_reason.max' => 'Lý do hủy không được vượt quá :max ký tự.',
            ]);
            Log::info('Lý do hủy đã xác thực: ' . $validatedData['cancel_reason']);

            // Kiểm tra xem đơn hàng có thể hủy không
            $canBeCancelled = $order->canBeCancelled();
            Log::info('Kết quả canBeCancelled(): ' . ($canBeCancelled ? 'true' : 'false'));

            if (!$canBeCancelled) {
                Log::warning("Đơn hàng {$order->id} không thể hủy vì trạng thái hiện tại: {$order->order_status}");
                return response()->json(['success' => false, 'message' => 'Đơn hàng này không thể hủy.'], 400);
            }

            // Kiểm tra nếu đơn hàng đã ở trạng thái 'Hủy đơn'
            if ($order->order_status === 'Hủy đơn') {
                Log::warning("Đơn hàng {$order->id} đã được hủy trước đó.");
                return response()->json(['success' => false, 'message' => 'Đơn hàng này đã được hủy trước đó.'], 400);
            }

            $order->order_status = 'Hủy đơn'; // Cập nhật trạng thái enum
            $order->cancel_reason = $validatedData['cancel_reason'];
            $order->save();
            Log::info("Đơn hàng {$order->id} đã được hủy thành công.");

            return response()->json(['success' => true, 'message' => 'Đơn hàng #' . ($order->sku ?? $order->id) . ' đã được hủy thành công.']);
        } catch (ValidationException $e) {
            Log::warning("Validation error during order cancellation for order {$order->id}: " . json_encode($e->errors()));
            return response()->json(['success' => false, 'message' => 'Lỗi xác thực.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("LỖI NGHIÊM TRỌNG khi hủy đơn hàng {$order->id}: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi khi hủy đơn hàng.'], 500);
        }
    }
}
