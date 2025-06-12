<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use App\Models\Order;
use App\Models\Discount;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use App\Models\DiscountProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class OrderController extends Controller
{
    /**
     * Lấy danh sách các trạng thái đơn hàng ENUM (có thể dùng chung).
     * @return array
     */
    protected function getOrderEnumStatuses(): array
    {
        return ['Chưa xác nhận', 'Xác nhận', 'Đang vận chuyển', 'Giao hàng thành công', 'Hủy đơn'];
    }

    /**
     * Lấy danh sách các trạng thái thanh toán ENUM (có thể dùng chung).
     * @return array
     */
    protected function getPaymentEnumStatuses(): array
    {
        return ['pending', 'paid', 'failed'];
    }

    /**
     * Map trạng thái thanh toán tiếng Anh sang tiếng Việt.
     * @param string|null $status
     * @return string
     */
    protected function mapPaymentStatusToVietnamese(?string $status): string
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
            'discount',
            'paymentMethod',
            'items.productVariant.product',
            'items.productVariant.productVariantValues.attributeValue', // Đã sửa tên quan hệ ở model ProductVariant
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

        // Lấy kết quả phân trang, bao gồm cả những đơn hàng đã xóa mềm (nếu cần hiển thị)
        $orders = $query->withTrashed()->paginate(20)->withQueryString();

        $orderStatuses = $this->getOrderEnumStatuses();
        $paymentMethods = PaymentMethod::all();
        $paymentStatuses = $this->getPaymentEnumStatuses();

        return view('admin.orders.index', compact('orders', 'orderStatuses', 'paymentMethods', 'paymentStatuses'));
    }

    public function create()
    {
        $users = User::all();
        $productVariants = ProductVariant::with('product:id,name')->where('status', 1)->get();
        $discounts = Discount::where('status', 'active')->get();
        $payMethods = PaymentMethod::all();

        $productVariantsForJs = $productVariants->mapWithKeys(function ($variant) {
            return [
                $variant->id => [
                    'price' => (float) $variant->price,
                    'name' => $variant->product->name,
                    'sku' => $variant->sku
                ]
            ];
        });

        $discountsForJs = $discounts->mapWithKeys(function ($discount) {
            return [
                $discount->id => [
                    'type' => $discount->discount_type,
                    'value' => (float) $discount->discount_value,
                    'maxValue' => (float) ($discount->max_discount ?? 0),
                    'minValue' => (float) ($discount->min_order_value ?? 0),
                    'applies_to_all_products' => (int) $discount->applies_to_all_products,
                    'code' => $discount->code
                ]
            ];
        });
        $discountProductsMap = DiscountProduct::select('discount_id', 'product_id')->get()->groupBy('discount_id')->map(function ($items) {
            return $items->pluck('product_id')->toArray();
        });

        return view('admin.orders.create', compact(
            'users',
            'productVariants',
            'discounts',
            'payMethods',
            'productVariantsForJs',
            'discountsForJs',
            'discountProductsMap'
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
                $variant = ProductVariant::with('product:id,name,slug,image')->find($variantId); // Eager load product info + image

                if (!$variant) {
                    return redirect()->back()->withErrors(['products.' . $index => "Sản phẩm không tồn tại (ID: {$variantId})."])->withInput();
                }

                $quantityToOrder = (int) $requestedQuantities[$index];

                if ($variant->quantity < $quantityToOrder) {
                    return redirect()->back()
                        ->withErrors(['products.' . $index => 'Sản phẩm "' . $variant->product->name . ' (SKU: ' . $variant->sku . ')" không đủ số lượng tồn kho (còn ' . $variant->quantity . ').'])
                        ->withInput();
                }

                $itemTotal = $variant->price * $quantityToOrder;
                $subTotal += $itemTotal;

                $cartItemsDetails[] = [
                    'product_variant_id' => $variantId,
                    'product_id' => $variant->product_id,
                    'quantity' => $quantityToOrder,
                    'unit_price' => $variant->price,
                    'total_price' => $itemTotal,
                    'variant_instance' => $variant,
                ];
            }

            $discountAmount = 0;
            $appliedDiscountId = null;
            $discountModelInstance = null;

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
                if ($subTotal > ($discountModelInstance->max_order_value ?? PHP_INT_MAX)) {
                    return redirect()->back()->withErrors(['discount_id' => 'Tổng giá trị đơn hàng vượt quá giới hạn tối đa cho phép của mã giảm giá.'])->withInput();
                }

                if ($discountModelInstance->user_usage_limit > 0) {
                    $userUsesCount = Order::where('user_id', $request->user_id)
                        ->where('discount_id', $discountModelInstance->id)
                        ->where('order_status', '!=', 'Hủy đơn')
                        ->count();
                    if ($userUsesCount >= $discountModelInstance->user_usage_limit) {
                        return redirect()->back()->withErrors(['discount_id' => 'Bạn đã sử dụng hết số lần cho phép của mã giảm giá này.'])->withInput();
                    }
                }

                $amountEligibleForDiscount = $subTotal;
                $productDiscountQuantity = 0;

                if (!$discountModelInstance->applies_to_all_products) {
                    $applicableProductIds = $discountModelInstance->products()->pluck('products.id')->toArray();

                    $currentApplicableItemsTotal = 0;
                    $hasApplicableItemInCart = false;

                    foreach ($cartItemsDetails as $cartItem) {
                        if (in_array($cartItem['product_id'], $applicableProductIds)) {
                            $currentApplicableItemsTotal += $cartItem['total_price'];
                            $hasApplicableItemInCart = true;
                            $productDiscountQuantity += $cartItem['quantity'];
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
                    $discountAmount = $discountModelInstance->discount_value;
                    $discountAmount = min($discountAmount, $discountModelInstance->max_discount);
                }

                $discountAmount = min($discountAmount, $subTotal);
                $appliedDiscountId = $discountModelInstance->id;
            }

            $totalAfterDiscount = max(0, $subTotal - $discountAmount);
            $grandTotal = $totalAfterDiscount + $request->input('shipping_fee', 0);

            do {
                $orderSku = 'DH-' . strtoupper(Str::random(2)) . now()->format('ymd') . rand(100, 999);
            } while (Order::where('sku', $orderSku)->exists());

            $paymentMethod = PaymentMethod::findOrFail($request->payment_method_id);

            $order = Order::create([
                'user_id' => $request->user_id,
                'user_name' => User::find($request->user_id)->name,
                'sku' => $orderSku,
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'order_status' => 'Chưa xác nhận',
                'discount_id' => $appliedDiscountId,
                'payment_method_id' => $request->payment_method_id,
                'discount_code' => optional($discountModelInstance)->code,
                'discount_value' => optional($discountModelInstance)->discount_value,
                'payment_method_name' => $paymentMethod->name,
                'payment_status' => 'pending',
                'discount_amount' => $discountAmount,
                'shipping_fee' => $request->shipping_fee,
                'total_amount' => $grandTotal,
                'note' => $request->note,
            ]);

            $orderItemsToSave = [];
            foreach ($cartItemsDetails as $itemDetail) {
                $variant = $itemDetail['variant_instance'];

                // Lấy các thuộc tính của biến thể
                $productAttributeNames = $variant->productVariantValues->map(function ($pvv) {
                    return optional($pvv->attributeValue)->value;
                })->filter()->implode(' - ');

                $orderItemsToSave[] = new OrderItem([
                    'product_variant_id' => $itemDetail['product_variant_id'],
                    'product_name' => $variant->product->name . ($productAttributeNames ? ' (' . $productAttributeNames . ')' : ''),
                    'product_variant_sku' => $variant->sku,
                    'product_attribute' => $productAttributeNames,
                    'quantity' => $itemDetail['quantity'],
                    'unit_price' => $itemDetail['unit_price'],
                    'discount_amount' => 0.00,
                    'total_price' => $itemDetail['total_price'],
                ]);

                // Giảm tồn kho cho biến thể sản phẩm
                $variant->decrement('quantity', $itemDetail['quantity']);
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
            'discount.products',
            'paymentMethod',
            'items' => function ($query) {
                $query->withTrashed();
            },
            'items.productVariant' => function ($query) {
                $query->withTrashed();
            },
            'items.productVariant.product' => function ($query) {
                $query->withTrashed();
            },
            'items.productVariant.productVariantValues.attributeValue',
        ])
            ->withTrashed()
            ->findOrFail($id);

        $discountProductIds = $order->discount?->products->pluck('id')->toArray() ?? [];

        $allOrderStatuses = $this->getOrderEnumStatuses();
        $paymentStatuses = $this->getPaymentEnumStatuses();

        return view('admin.orders.show', compact('order', 'discountProductIds', 'allOrderStatuses', 'paymentStatuses'));
    }


    public function updateStatus(Request $request, $id)
    {
        Log::info('--- Order Update Status Attempt ---');
        Log::info('Order ID: ' . $id);
        Log::info('Request Data: ', $request->all());

        $order = Order::findOrFail($id);
        $newOrderStatus = $request->input('order_status');
        $oldOrderStatus = $order->order_status;

        // Định nghĩa mảng map để dịch trong controller nếu cần cho log/messages
        $paymentStatusMap = [
            'pending' => 'Chờ thanh toán',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thất bại',
        ];

        Log::info("Old Order Status: '{$oldOrderStatus}'");
        Log::info("New Order Status from Request: '{$newOrderStatus}'");
        Log::info("Cancel Reason from Request: " . $request->input('cancel_reason'));

        $validator = Validator::make($request->all(), [
            'order_status' => 'required|in:' . implode(',', $this->getOrderEnumStatuses()),
            'cancel_reason' => 'nullable|string|min:10', // Lý do hủy chỉ validate nếu nó được gửi
        ], [
            'order_status.required' => 'Trạng thái đơn hàng không được để trống.',
            'order_status.in' => 'Trạng thái đơn hàng không hợp lệ.',
            'cancel_reason.min' => 'Lý do hủy phải có ít nhất 10 ký tự.',
        ]);

        // Logic để không cho chọn lại trạng thái trước
        $currentStatusIndex = array_search($oldOrderStatus, $this->getOrderEnumStatuses());
        $newStatusIndex = array_search($newOrderStatus, $this->getOrderEnumStatuses());

        $progressingStatuses = ['Chưa xác nhận', 'Xác nhận', 'Đang vận chuyển', 'Giao hàng thành công'];

        if (in_array($newOrderStatus, $progressingStatuses) && $newStatusIndex < $currentStatusIndex) {
            $validator->after(function ($validator) use ($oldOrderStatus, $newOrderStatus) {
                $validator->errors()->add('order_status', "Không thể chuyển từ '{$oldOrderStatus}' về trạng thái '{$newOrderStatus}' (trạng thái lùi).");
            });
        }
        // Cho phép chuyển sang 'Hủy đơn' nếu đơn hàng có thể hủy
        elseif ($newOrderStatus === 'Hủy đơn' && !$order->canBeCancelled()) {
            $validator->after(function ($validator) {
                $validator->errors()->add('order_status', "Đơn hàng này không thể chuyển sang trạng thái 'Hủy đơn'.");
            });
        }
        // Kiểm tra bắt buộc lý do hủy nếu trạng thái mới là 'Hủy đơn'
        elseif ($newOrderStatus === 'Hủy đơn' && empty($request->input('cancel_reason'))) {
            $validator->after(function ($validator) {
                $validator->errors()->add('cancel_reason', 'Vui lòng cung cấp lý do hủy nếu chọn trạng thái "Hủy đơn".');
            });
        }

        if ($validator->fails()) {
            // Đối với AJAX, trả về JSON với lỗi
            return response()->json(['success' => false, 'message' => 'Lỗi xác thực.', 'errors' => $validator->errors()], 422);
        }

        try {
            $order->order_status = $newOrderStatus;
            if ($newOrderStatus === 'Hủy đơn') {
                $order->cancel_reason = $request->input('cancel_reason');
            } else {
                $order->cancel_reason = null;
            }
            $order->save();
            Log::info('Order status updated successfully in DB.');

            return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái đơn hàng thành công!']);
        } catch (\Exception $e) {
            Log::error("Lỗi khi cập nhật trạng thái đơn hàng {$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi khi cập nhật trạng thái đơn hàng.'], 500);
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
            return response()->json(['success' => false, 'message' => 'Lỗi xác thực.', 'errors' => $validator->errors()], 422);
        }

        try {
            $order->payment_status = $request->payment_status;
            $order->save();

            return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thanh toán thành công!']);
        } catch (\Exception $e) {
            Log::error("Lỗi khi cập nhật trạng thái thanh toán đơn hàng {$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi khi cập nhật trạng thái thanh toán.'], 500);
        }
    }

    public function edit($id)
    {
        $order = Order::with([
            'user.profile',
            'discount.products',
            'items.productVariant.product',
            'paymentMethod'
        ])->findOrFail($id);

        $allOrderStatuses = $this->getOrderEnumStatuses();
        $paymentMethods = PaymentMethod::all();
        $paymentStatuses = $this->getPaymentEnumStatuses(); // Thêm vào để sử dụng

        $discountProductIds = $order->discount?->products->pluck('id')->toArray() ?? [];

        return view('admin.orders.edit', compact('order', 'allOrderStatuses', 'paymentMethods', 'paymentStatuses', 'discountProductIds'));
    }

    /**
     * Cập nhật thông tin đơn hàng trong database.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id ID của đơn hàng.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:15',
            'shipping_address' => 'required|string|max:255',
            'order_status' => 'required|in:' . implode(',', $this->getOrderEnumStatuses()), // Sửa
            'payment_status' => 'required|in:' . implode(',', $this->getPaymentEnumStatuses()), // Thêm
            'total_amount' => 'required|numeric|min:0|max:99999999.99',
            'note' => 'nullable|string',
            'cancel_reason' => 'nullable|string|min:10',
            'discount_id' => 'nullable|exists:discounts,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'shipping_fee' => 'required|numeric|min:0',
        ], [
            'shipping_name.required' => 'Tên người nhận không được để trống.',
            'shipping_phone.required' => 'Số điện thoại không được để trống.',
            'shipping_address.required' => 'Địa chỉ không được để trống.',
            'order_status.required' => 'Trạng thái đơn hàng không được để trống.',
            'order_status.in' => 'Trạng thái đơn hàng không hợp lệ.',
            'payment_status.required' => 'Trạng thái thanh toán không được để trống.',
            'payment_status.in' => 'Trạng thái thanh toán không hợp lệ.',
            'total_amount.required' => 'Tổng tiền không được để trống.',
            'total_amount.numeric' => 'Tổng tiền phải là số.',
            'cancel_reason.min' => 'Lý do hủy phải có ít nhất 10 ký tự.',
            'payment_method_id.required' => 'Phương thức thanh toán không được để trống.',
            'payment_method_id.exists' => 'Phương thức thanh toán không tồn tại.',
            'shipping_fee.required' => 'Phí vận chuyển không được để trống.',
            'shipping_fee.numeric' => 'Phí vận chuyển phải là số.',
        ]);

        // Logic validation bổ sung cho order_status và cancel_reason
        $oldOrderStatus = $order->order_status;
        $newOrderStatus = $request->input('order_status');
        $orderStatusesEnum = $this->getOrderEnumStatuses();
        $currentStatusIndex = array_search($oldOrderStatus, $orderStatusesEnum);
        $newStatusIndex = array_search($newOrderStatus, $orderStatusesEnum);
        $progressingOrderStatuses = ['Chưa xác nhận', 'Xác nhận', 'Đang vận chuyển', 'Giao hàng thành công'];

        if (in_array($newOrderStatus, $progressingOrderStatuses) && $newStatusIndex < $currentStatusIndex) {
            $validator->after(function ($validator) use ($oldOrderStatus, $newOrderStatus) {
                $validator->errors()->add('order_status', "Không thể chuyển từ '{$oldOrderStatus}' về trạng thái '{$newOrderStatus}' (trạng thái lùi).");
            });
        } elseif ($newOrderStatus === 'Hủy đơn' && !$order->canBeCancelled()) {
            $validator->after(function ($validator) {
                $validator->errors()->add('order_status', "Đơn hàng này không thể chuyển sang trạng thái 'Hủy đơn'.");
            });
        } elseif ($newOrderStatus === 'Hủy đơn' && empty($request->input('cancel_reason'))) {
            $validator->after(function ($validator) {
                $validator->errors()->add('cancel_reason', 'Vui lòng cung cấp lý do hủy nếu chọn trạng thái "Hủy đơn".');
            });
        }

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $order->shipping_name = $request->input('shipping_name');
            $order->shipping_phone = $request->input('shipping_phone');
            $order->shipping_address = $request->input('shipping_address');
            $order->order_status = $request->input('order_status');
            $order->payment_status = $request->input('payment_status');
            $order->total_amount = $request->input('total_amount');
            $order->note = $request->input('note');

            if ($order->order_status === 'Hủy đơn') {
                $order->cancel_reason = $request->input('cancel_reason');
            } else {
                $order->cancel_reason = null;
            }

            $order->discount_id = $request->input('discount_id');
            $order->payment_method_id = $request->input('payment_method_id');
            $order->shipping_fee = $request->input('shipping_fee');

            $order->save();

            return redirect()->route('admin.orders.index')->with('success', 'Cập nhật đơn hàng thành công!');
        } catch (\Exception $e) {
            Log::error("Lỗi khi cập nhật đơn hàng {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi cập nhật đơn hàng.')->withInput();
        }
    }

    public function trash()
    {
        // Eager load các mối quan hệ cần thiết để hiển thị thông tin
        $orders = Order::onlyTrashed()->with([
            'user', // Thông tin người dùng đặt hàng
            'discount', // Thông tin mã giảm giá (nếu có)
            'paymentMethod', // Thông tin phương thức thanh toán
            'items.productVariant.product', // Chi tiết sản phẩm trong đơn hàng
        ])->latest()->paginate(20); // Có thể phân trang cho trang thùng rác

        return view('admin.orders.trash', compact('orders'));
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        try {
            $order->delete(); // Thực hiện soft delete
            return redirect()->back()->with('success', 'Đã xóa mềm đơn hàng!');
        } catch (\Exception $e) {
            Log::error("Lỗi khi xóa mềm đơn hàng {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa mềm đơn hàng.');
        }
    }

    public function restore($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        try {
            $order->restore();
            return redirect()->route('admin.orders.trash')->with('success', 'Khôi phục đơn hàng thành công!');
        } catch (\Exception $e) {
            Log::error("Lỗi khi khôi phục đơn hàng {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi khôi phục đơn hàng.');
        }
    }

    public function forceDelete($id)
    {
        $order = Order::withTrashed()->findOrFail($id);
        try {
            $order->forceDelete();
            return redirect()->route('admin.orders.trash')->with('success', 'Đã xóa vĩnh viễn đơn hàng!');
        } catch (\Exception $e) {
            Log::error("Lỗi khi xóa vĩnh viễn đơn hàng {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa vĩnh viễn đơn hàng.');
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
