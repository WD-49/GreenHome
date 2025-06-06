<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Discount;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use Illuminate\Support\Str;
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
        $productVariants = ProductVariant::with('product:id,name')->where('status', 1)->get();
        $discounts = Discount::where('status', 'active') /* ... các điều kiện khác ... */->get();

        $productVariantsForJs = $productVariants->mapWithKeys(function ($variant) {
            return [$variant->id => [
                'price' => (float) $variant->price,
                'name' => $variant->product->name, // Để hiển thị nếu cần
                'sku' => $variant->sku
            ]];
        });

        $discountsForJs = $discounts->mapWithKeys(function ($discount) {
            return [$discount->id => [
                'type' => $discount->discount_type,
                'value' => (float) $discount->discount_value,
                'maxValue' => (float) ($discount->max_discount ?? 0),
                'minValue' => (float) ($discount->min_order_value ?? 0),
                'code' => $discount->code // Có thể hữu ích để hiển thị
            ]];
        });

        return view('admin.orders.create', compact(
            'users',
            'productVariants', // Vẫn truyền productVariants cho vòng lặp select HTML
            'discounts',       // Vẫn truyền discounts cho vòng lặp select HTML
            'payMethods',
            'productVariantsForJs', // Dữ liệu cho JS
            'discountsForJs'        // Dữ liệu cho JS
        ));
    }

    /**
     * Lưu đơn hàng mới vào database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|regex:/^[0-9]{10,15}$/',
            'shipping_address' => 'required|string|max:255',
            'products' => 'required|array|min:1',
            'products.*' => 'required|exists:product_variants,id',
            'quantities' => 'required|array|min:1',
            'quantities.*' => 'required|integer|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'shipping_fee' => 'required|numeric|min:0',
            'discount_id' => 'nullable|exists:discounts,id',
            'note' => 'nullable|string|max:1000',
        ], [
            'user_id.required' => 'Vui lòng chọn khách hàng.',
            'shipping_name.required' => 'Tên người nhận không được để trống.',
            'shipping_phone.required' => 'Số điện thoại không được để trống.',
            'shipping_phone.regex' => 'Số điện thoại không hợp lệ.',
            'shipping_address.required' => 'Địa chỉ không được để trống.',
            'products.required' => 'Vui lòng chọn ít nhất một sản phẩm.',
            'products.*.required' => 'Có lỗi trong việc chọn sản phẩm.',
            'products.*.exists' => 'Sản phẩm được chọn không hợp lệ.',
            'quantities.required' => 'Vui lòng nhập số lượng cho sản phẩm.',
            'quantities.*.required' => 'Vui lòng nhập số lượng cho mỗi sản phẩm.',
            'quantities.*.min' => 'Số lượng sản phẩm phải lớn hơn 0.',
            'payment_method_id.required' => 'Vui lòng chọn phương thức thanh toán.',
            'shipping_fee.required' => 'Vui lòng nhập phí vận chuyển.',
            'discount_id.exists' => 'Mã giảm giá không hợp lệ.',
            'note.max' => 'Ghi chú không được vượt quá 1000 ký tự.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (count($request->input('products')) !== count($request->input('quantities'))) {
            return redirect()->back()->withErrors(['products' => 'Dữ liệu sản phẩm và số lượng không khớp.'])->withInput();
        }

        return DB::transaction(function () use ($request) {
            $productVariantInputIds = $request->input('products');
            $requestedQuantities = $request->input('quantities');
            $subTotal = 0; // Tổng tiền hàng trước giảm giá và phí ship
            $cartItemsDetails = []; // Mảng để lưu thông tin chi tiết các item sẽ tạo và biến thể để cập nhật

            foreach ($productVariantInputIds as $index => $variantId) {
                $variant = ProductVariant::with('product:id,name,slug')->find($variantId); // Eager load product info

                if (!$variant) {
                    // Lỗi này gần như không xảy ra nếu validation 'exists' hoạt động đúng
                    return redirect()->back()->withErrors(['products.' . $index => "Sản phẩm không tồn tại (ID: {$variantId})."])->withInput();
                }

                $quantityToOrder = (int)$requestedQuantities[$index];

                if ($variant->quantity < $quantityToOrder) { // Kiểm tra tồn kho bằng cột 'quantity' của product_variants
                    return redirect()->back()
                        ->withErrors(['products.' . $index => 'Sản phẩm "' . $variant->product->name . ' (SKU: ' . $variant->sku . ')" không đủ số lượng tồn kho (còn ' . $variant->quantity . ').'])
                        ->withInput();
                }

                $itemTotal = $variant->price * $quantityToOrder;
                $subTotal += $itemTotal;

                $cartItemsDetails[] = [
                    'product_variant_id' => $variantId,
                    'product_id' => $variant->product_id, // Lưu product_id để check discount theo product
                    'quantity' => $quantityToOrder,
                    'unit_price' => $variant->price,
                    'total_price' => $itemTotal,
                    'variant_instance' => $variant, // Giữ lại instance để cập nhật stock
                ];
            }

            $discountAmount = 0;
            $appliedDiscountId = null;
            $discountModelInstance = null; // Biến để giữ model Discount nếu được áp dụng

            if ($request->filled('discount_id')) {
                $discountModelInstance = Discount::where('id', $request->discount_id)
                    ->where('status', 'active')
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                if (!$discountModelInstance) {
                    return redirect()->back()->withErrors(['discount_id' => 'Mã giảm giá không hợp lệ, đã hết hạn hoặc không tồn tại.'])->withInput();
                }

                if ($discountModelInstance->quantity <= 0) {
                    return redirect()->back()->withErrors(['discount_id' => 'Mã giảm giá đã hết lượt sử dụng.'])->withInput();
                }

                // Kiểm tra giới hạn sử dụng của người dùng cho mã này
                if ($discountModelInstance->user_usage_limit > 0) {
                    $userUsesCount = Order::where('user_id', $request->user_id)
                        ->where('discount_id', $discountModelInstance->id)
                        // ->whereNotIn('status_id', [ID_TRANG_THAI_DA_HUY]) // Nếu có trạng thái hủy cụ thể
                        ->count();
                    if ($userUsesCount >= $discountModelInstance->user_usage_limit) {
                        return redirect()->back()->withErrors(['discount_id' => 'Bạn đã sử dụng hết số lần cho phép của mã giảm giá này.'])->withInput();
                    }
                }

                $amountEligibleForDiscount = $subTotal; // Số tiền sẽ được tính giảm giá

                if (!$discountModelInstance->applies_to_all_products) {
                    // Logic này dựa trên bảng discount_products (discount_id, product_id)
                    // Cần load relationship products() cho $discountModelInstance
                    // Trong Discount model: public function products() { return $this->belongsToMany(Product::class, 'discount_products'); }
                    $applicableProductIds = $discountModelInstance->products()->pluck('products.id')->toArray();

                    $currentApplicableItemsTotal = 0;
                    $hasApplicableItemInCart = false;

                    foreach ($cartItemsDetails as $cartItem) {
                        if (in_array($cartItem['product_id'], $applicableProductIds)) {
                            $currentApplicableItemsTotal += $cartItem['total_price'];
                            $hasApplicableItemInCart = true;
                        }
                    }

                    if (!$hasApplicableItemInCart) {
                        return redirect()->back()->withErrors(['discount_id' => 'Mã giảm giá không áp dụng cho bất kỳ sản phẩm nào trong giỏ hàng.'])->withInput();
                    }
                    $amountEligibleForDiscount = $currentApplicableItemsTotal;
                }

                if ($amountEligibleForDiscount < $discountModelInstance->min_order_value) {
                    $formattedMinOrderValue = number_format($discountModelInstance->min_order_value, 0, ',', '.') . 'đ';
                    $errorMessage = $discountModelInstance->applies_to_all_products ?
                        "Đơn hàng chưa đủ giá trị tối thiểu ({$formattedMinOrderValue}) để áp dụng mã giảm giá." :
                        "Tổng giá trị các sản phẩm hợp lệ cho mã giảm giá chưa đủ giá trị tối thiểu ({$formattedMinOrderValue}).";
                    return redirect()->back()->withErrors(['discount_id' => $errorMessage])->withInput();
                }

                if ($discountModelInstance->discount_type === 'percentage') {
                    $rawDiscount = $amountEligibleForDiscount * ($discountModelInstance->discount_value / 100);
                    $discountAmount = min($rawDiscount, $discountModelInstance->max_discount);
                } elseif ($discountModelInstance->discount_type === 'fixed') {
                    $discountAmount = min($discountModelInstance->discount_value, $amountEligibleForDiscount);
                }
                $discountAmount = min($discountAmount, $subTotal); // Đảm bảo giảm giá không lớn hơn tổng tiền hàng
                $appliedDiscountId = $discountModelInstance->id;
            }

            $totalAfterDiscount = max(0, $subTotal - $discountAmount);
            $grandTotal = $totalAfterDiscount + $request->input('shipping_fee', 0);

            do {
                $orderSku = 'DH-' . strtoupper(Str::random(2)) . now()->format('ymd') . rand(100, 999);
            } while (Order::where('sku', $orderSku)->exists());

            $order = Order::create([
                'user_id' => $request->user_id,
                'sku' => $orderSku,
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'status_id' => 1, // ID trạng thái mặc định (ví dụ: Chờ xác nhận)
                'discount_id' => $appliedDiscountId,
                'payment_method_id' => $request->payment_method_id,
                'payment_status' => 'pending', // Có thể thay đổi dựa trên PT thanh toán
                'discount_amount' => $discountAmount,
                'shipping_fee' => $request->shipping_fee,
                'sub_total' => $subTotal, // Tổng tiền hàng (trước discount, trước ship)
                'total_amount' => $grandTotal, // Tổng cuối cùng khách phải trả
                'note' => $request->note,
                // 'created_by' => auth()->id(), // Nếu admin đang đăng nhập và tạo đơn
            ]);

            $orderItemsToSave = [];
            foreach ($cartItemsDetails as $itemDetail) {
                $orderItemsToSave[] = new OrderItem([
                    'product_variant_id' => $itemDetail['product_variant_id'],
                    'quantity' => $itemDetail['quantity'],
                    'unit_price' => $itemDetail['unit_price'],
                    'total_price' => $itemDetail['total_price'],
                ]);

                // Giảm tồn kho cho biến thể sản phẩm
                $variantInstance = $itemDetail['variant_instance'];
                $variantInstance->decrement('quantity', $itemDetail['quantity']); // Sử dụng cột 'quantity'
            }
            $order->items()->saveMany($orderItemsToSave);

            // Giảm số lượng sử dụng của mã giảm giá (nếu có)
            if ($discountModelInstance && $discountAmount > 0) {
                $discountModelInstance->decrement('quantity');
            }

            return redirect()->route('admin.orders.index')->with('success', 'Tạo đơn hàng thành công với mã: ' . $order->sku);
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
