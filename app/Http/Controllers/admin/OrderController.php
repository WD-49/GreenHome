<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Discount;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'discount', 'items', 'status', 'paymentMethod']);

        // Lọc theo mã đơn hàng
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

        // Lọc theo trạng thái thanh toán (giả sử là trường 'payment_status' trong bảng orders)
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Lọc theo trạng thái đơn hàng (dựa vào quan hệ status)
        if ($request->filled('order_status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('id', $request->order_status);
                // hoặc theo tên: $q->where('name', $request->order_status);
            });
        }

        // Lọc theo ngày đặt (ngày bắt đầu và ngày kết thúc)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Lọc theo phương thức thanh toán
        if ($request->filled('payment_method')) {
            $query->where('payment_method_id', $request->payment_method);
        }

        $orders = $query->paginate(20)->withQueryString(); // phân trang 20 item/trang, giữ query filter trong url

        // Lấy danh sách trạng thái, phương thức thanh toán để đổ vào filter dropdown
        $orderStatuses = \App\Models\OrderStatus::all();
        $paymentMethods = \App\Models\PaymentMethod::all();

        return view('admin.orders.index', compact('orders', 'orderStatuses', 'paymentMethods'));
    }

    public function create()
    {
        $users = User::all();
        $productVariants = ProductVariant::with('product')->get();
        $discounts = Discount::all();
        $payMethods = PaymentMethod::all();
        // dd($discounts);
        return view('admin.orders.create', compact('users', 'productVariants', 'discounts', 'payMethods'));
    }

    public function store(Request $request)
    {
        // dd($request);
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id', // Tạm bỏ qua kiểm tra user
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:15',
            'shipping_address' => 'required|string|max:255',
            'products' => 'required|array|min:1',
            'products.*' => 'exists:product_variants,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'integer|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'shipping_fee' => 'required|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0|max:99999999',
            'discount_id' => 'nullable|exists:discounts,id',
            'note' => 'nullable|string',
        ], [
            'user_id.required' => 'Vui lòng chọn khách hàng',
            'shipping_name.required' => 'Tên người nhận không được để trống',
            'shipping_phone.required' => 'Số điện thoại không được để trống',
            'shipping_address.required' => 'Địa chỉ không được để trống',
            'products.required' => 'Vui lòng chọn ít nhất 1 sản phẩm',
            'quantities.required' => 'Vui lòng nhập số lượng cho từng sản phẩm',
            'payment_method_id.required' => 'Vui lòng chọn phương thức thanh toán',
            'shipping_fee.required' => 'Vui lòng nhập phí vận chuyển',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        return DB::transaction(function () use ($request) {
            $products = $request->input('products');
            $quantities = $request->input('quantities');
            $totalBeforeDiscount = 0;
            $items = [];

            foreach ($products as $index => $variantId) {
                $variant = ProductVariant::findOrFail($variantId);
                $quantity = $quantities[$index];
                $itemTotal = $variant->price * $quantity;
                $totalBeforeDiscount += $itemTotal;
                // dd($totalBeforeDiscount);

                $items[] = [
                    'product_variant_id' => $variantId,
                    'unit_price' => $variant->price,
                    'total_price' => $itemTotal,
                    'price' => $variant->price,
                    'quantity' => $quantity,
                    'total' => $itemTotal,
                ];
            }

            $discountAmount = 0;
            $discount = null;

            if ($request->filled('discount_id')) {
                $discount = Discount::where('id', $request->discount_id)->where('status', 'active')->where('start_date', '<=', now())->where('end_date', '>=', now())->first();
                // dd($discount);
                // dd($date = now());
                // dd(Discount::find($request->discount_id));

                if (!$discount) {
                    return redirect()->back()->withErrors(['discount_id' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.'])->withInput();
                }

                if ($totalBeforeDiscount < $discount->min_order_value) {
                    return redirect()->back()->withErrors(['discount_id' => 'Đơn hàng chưa đủ điều kiện để áp dụng mã giảm giá.'])->withInput();
                }

                if ($discount->quantity <= 0) {
                    return redirect()->back()->withErrors(['discount_id' => 'Mã giảm giá đã hết lượt sử dụng.'])->withInput();
                }

                // BỎ kiểm tra lượt dùng theo user
                if ($discount->discount_type === 'percentage') {
                    $rawDiscount = $totalBeforeDiscount * $discount->discount_value / 100;
                    // dd($rawDiscount);
                    // dd($discount->max_discount);
                    $discountAmount = min($rawDiscount, $discount->max_discount);
                } elseif ($discount->discount_type === 'fixed') {
                    $discountAmount = min($discount->discount_value, $totalBeforeDiscount);
                }
            }
            // dd($totalBeforeDiscount, $discountAmount);

            $discountedTotal = max(0, $totalBeforeDiscount - $discountAmount);
            $totalAmount = $discountedTotal + $request->shipping_fee;

            do {
                $sku = 'DH-' . rand(1000, 9999);
            } while (Order::where('sku', $sku)->exists());

            $order = Order::create([
                'user_id' => $request->user_id, // Tạm thời bỏ user_id
                'sku' => $sku,
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'payment_method_id' => $request->payment_method_id,
                'payment_status' => 'pending',
                'status_id' => 1,
                'discount_id' => $request->discount_id,
                'discount_amount' => $discountAmount,
                'shipping_fee' => $request->shipping_fee,
                'total_amount' => $totalAmount,
                'note' => $request->note,
            ]);

            $order->items()->createMany($items);
            // dd($order);

            return redirect()->route('admin.orders.index')->with('success', 'Tạo đơn hàng thành công!');
        });
    }

    public function show($id)
    {
        $order = Order::with([
            'user.profile',
            'discount',
            'items.productVariant.product',
            'status',
            'paymentMethod'
        ])->findOrFail($id);
        // dd($order);
        $statuses = OrderStatus::all();
        // dd($order);
        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function updateStatus(Request $request, $id)
    {
        Log::info('--- Order Update Status Attempt ---');
        Log::info('Order ID: ' . $id);
        Log::info('Request Data: ', $request->all()); // Log tất cả dữ liệu request

        $order = Order::findOrFail($id);
        $newStatusId = $request->input('status_id');

        // Lấy thông tin model của trạng thái mới và trạng thái cũ
        $newStatusModel = OrderStatus::find($newStatusId);
        $oldStatusModel = $order->status; // Giả sử $order->status là relationship

        $newStatusName = $newStatusModel ? trim(mb_strtolower($newStatusModel->name, 'UTF-8')) : null;
        $oldStatusName = $oldStatusModel ? trim(mb_strtolower($oldStatusModel->name, 'UTF-8')) : null;

        Log::info("Old Status: ID={$order->status_id}, Name='{$oldStatusName}'");
        Log::info("New Status: ID={$newStatusId}, Name='{$newStatusName}'");
        Log::info("Cancel Reason from Request: " . $request->input('cancel_reason'));

        $order->status_id = $newStatusId;

        // 1. Nếu trạng thái mới là "Đã hủy"
        if ($newStatusName === 'đã hủy') {
            Log::info('Condition MET: New status IS "đã hủy".');
            $request->validate([
                'cancel_reason' => 'required|string|min:5|max:500', // Giảm min để dễ test, tăng max
            ], [
                'cancel_reason.required' => 'Vui lòng nhập lý do hủy đơn hàng.',
                'cancel_reason.min' => 'Lý do hủy phải có ít nhất :min ký tự.',
                'cancel_reason.max' => 'Lý do hủy không được vượt quá :max ký tự.',
            ]);
            $order->cancel_reason = $request->input('cancel_reason');
            Log::info('Cancel reason to be saved: ' . $order->cancel_reason);
        }
        // 2. Nếu trạng thái cũ là "Đã hủy" VÀ trạng thái mới KHÁC "Đã hủy"
        elseif ($oldStatusName === 'đã hủy' && $newStatusName !== 'đã hủy') {
            Log::info('Condition MET: Old status WAS "đã hủy" and new status IS NOT "đã hủy". Clearing cancel_reason.');
            $order->cancel_reason = null;
        } else {
            Log::info('Condition for cancel_reason NOT MET or no change needed.');
        }

        $order->save();
        Log::info('Order status updated successfully in DB.');

        return redirect()->back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }

    public function updatePaymentStatus(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $order = Order::findOrFail($id);
        $order->payment_status = $request->payment_status;
        $order->save();

        return redirect()->back()->with('success', 'Cập nhật trạng thái thanh toán thành công!');
    }

    public function edit($id)
    {
        $order = Order::with(['user', 'discount', 'items', 'status', 'paymentMethod'])->findOrFail($id);
        $statuses = OrderStatus::all();
        return view('admin.orders.edit', compact('order', 'statuses'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:15',
            'shipping_address' => 'required|string|max:255',
            'status_id' => 'required|exists:order_statuses,id',
            'total_amount' => 'required|numeric|min:0|max:99999999.99',
            'note' => 'nullable|string',
        ], [
            'shipping_name.required' => 'Tên người nhận không được để trống',
            'shipping_phone.required' => 'Số điện thoại không được để trống',
            'shipping_address.required' => 'Địa chỉ không được để trống',
            'status_id.required' => 'Trạng thái không được để trống',
            'total_amount.required' => 'Tổng tiền không được để trống',
            'total_amount.numeric' => 'Tổng tiền phải là số',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $order = Order::findOrFail($id);
        $order->update($request->only(['shipping_name', 'shipping_phone', 'shipping_address', 'status_id', 'total_amount', 'note']));

        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật đơn hàng thành công!');
    }

    public function trash()
    {
        $orders = Order::onlyTrashed()->with(['user', 'discount', 'items', 'status', 'paymentMethod'])->get();
        return view('admin.orders.trash', compact('orders'));
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return redirect()->back()->with('success', 'Đã xóa mềm đơn hàng!');
    }

    public function restore($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->restore();
        return redirect()->route('admin.orders.trash')->with('success', 'Khôi phục đơn hàng thành công!');
    }

    public function forceDelete($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        $order->forceDelete();
        return redirect()->route('orders.trash')->with('success', 'Đã xóa vĩnh viễn đơn hàng!');
    }

    public function cancel(Request $request, Order $order)
    {
        // Validate lý do hủy
        $validatedData = $request->validate([
            'cancel_reason' => 'required|string|min:10|max:1000', // << SỬA Ở ĐÂY (tên key)
        ], [
            'cancel_reason.required' => 'Vui lòng nhập lý do hủy đơn hàng.', // << SỬA Ở ĐÂY (tên key)
            'cancel_reason.min' => 'Lý do hủy phải có ít nhất :min ký tự.',    // << SỬA Ở ĐÂY (tên key)
            'cancel_reason.max' => 'Lý do hủy không được vượt quá :max ký tự.', // << SỬA Ở ĐÂY (tên key)
        ]);

        // Kiểm tra xem đơn hàng có thể hủy không
        if (method_exists($order, 'canBeCancelled') && !$order->canBeCancelled()) {
            return redirect()->route('admin.orders.index')->with('error', 'Đơn hàng này không thể hủy.');
        }

        $cancelledStatus = OrderStatus::where('name', 'Đã hủy')->first();

        if (!$cancelledStatus) {
            return redirect()->route('admin.orders.index')->with('error', 'Không tìm thấy trạng thái "Đã hủy". Vui lòng cấu hình.');
        }

        if ($order->status_id == $cancelledStatus->id) {
            return redirect()->route('admin.orders.index')->with('warning', 'Đơn hàng này đã được hủy trước đó.');
        }

        $order->status_id = $cancelledStatus->id;
        $order->cancel_reason = $validatedData['cancel_reason']; // << SỬA Ở ĐÂY (tên thuộc tính và key)
        // $order->cancelled_at = now();
        // $order->cancelled_by = auth()->id();
        $order->save();

        // ... (Logic hoàn kho, gửi thông báo nếu có) ...

        return redirect()->route('admin.orders.index')->with('success', 'Đơn hàng #' . ($order->sku ?? $order->id) . ' đã được hủy thành công.');
    }
}
