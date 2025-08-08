<?php

namespace App\Http\Controllers\client;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use App\Models\WebInfo;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DiscountUsage;
use App\Models\PaymentMethod;
use App\Mail\OrderInvoiceMail;
use App\Models\ProductVariant;
use App\Jobs\CancelVnpayOrderJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\PaymentController;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user()->load('profile');

        return view('client.pages.checkout', [
            'user' => $user,
        ]);
    }

    public function getCheckoutData(Request $request)
    {
        $user = Auth::user();

        $type = $request->query('type'); // full, selected, direct

        $cart = Cart::where(function ($q) {
            $q->where('user_id', Auth::user()->id);
        })
            ->with(['items.productVariant.product']) // eager load
            ->first();

        if (!$cart && $type !== 'direct') {
            return response()->json([
                'success' => false,
                'message' => 'Giỏ hàng không tồn tại.'
            ]);
        }



        switch ($type) {
            case 'selected':
                $ids = $request->query('ids', []);
                $cartItems = CartItem::with(['productVariant.product'])
                    ->where('cart_id', $cart->id)
                    ->whereIn('id', $ids)
                    ->get();
                break;

            case 'direct':
                $variantId = $request->query('variant_id');
                $quantity = $request->query('quantity', 1);
                $variant = ProductVariant::with('product')->findOrFail($variantId);
                $cartItems = collect([
                    (object)[
                        'product_variant' => $variant,
                        'quantity' => $quantity
                    ]
                ]);
                break;

            case 'full':
            default:
                $cartItems = $cart->items()->with(['productVariant.product'])->get();
                break;
        }

        // Lấy danh sách ID sản phẩm trong giỏ/đơn hàng
        $productIds = $cartItems->pluck('productVariant.product.id')->filter()->unique()->values()->all();
        // dd($productIds);

        // Lấy ID các mã giảm giá đã được user sử dụng
        $usedDiscountIds = DB::table('discount_usages')
            ->where('user_id', $user->id)
            ->groupBy('discount_id')
            ->select('discount_id', DB::raw('COUNT(*) as used_count'))
            ->pluck('used_count', 'discount_id');

        // dd($usedDiscountIds);

        // Lấy danh sách mã giảm giá đủ điều kiện
        $now = now();
        $validDiscounts = Discount::where('status', 'active')
            ->whereNull('deleted_at')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('quantity', '>', 0)
            ->with(['products:id']) // Eager load
            ->get()
            ->filter(function ($discount) use ($usedDiscountIds, $productIds) {
                // Kiểm tra giới hạn người dùng
                $used = $usedDiscountIds[$discount->id] ?? 0;
                if ($discount->user_usage_limit > 0 && $used >= $discount->user_usage_limit) {
                    return false;
                }

                // Nếu không áp dụng toàn bộ sản phẩm, kiểm tra sản phẩm cụ thể
                if ($discount->applies_to_all_products !== 1) {
                    $discountProductIds = $discount->products->pluck('id')->all();
                    $matched = array_intersect($discountProductIds, $productIds);
                    if (count($matched) === 0) {
                        return false;
                    }
                }

                return true;
            })
            ->values(); // Reset index
        // dd($validDiscounts);

        $paymentMethods = PaymentMethod::where('status', 1)->get();
        $shippingFee = WebInfo::where('key', 'delivery_cost')->first()->value;
        return response()->json([
            'success' => true,
            'items' => $cartItems,
            'paymentMethods' => $paymentMethods,
            'shippingFee' => $shippingFee,
            'validDiscounts' => $validDiscounts,
        ]);
    }
    public function submit(Request $request)
    {
        try {
            $data = $request->validate([
                'fullname' => 'required|string',
                'phone' => 'required|string',
                'ward_name' => 'required|string',
                'district_name' => 'required|string',
                'province_name' => 'required|string',
                'address_detail' => 'required|string',
                'note' => 'nullable|string',
                'payment_method_id' => 'required|exists:payment_methods,id',
                'final_total' => 'required|numeric',
                'discount_id' => 'nullable|exists:discounts,id',
                'discount_code' => 'nullable|string',
                'discount_amount' => 'nullable|numeric',
                'shipping_fee' => 'required|numeric',
                'items' => 'required|array|min:1',
            ]);

            DB::beginTransaction();

            $user = auth()->user();
            if (is_null($user->email_verified_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng xác minh email trước khi đặt hàng.',
                    'redirect_url' => route('profile.index'),
                ], 403);
            }

            $paymentMethod = PaymentMethod::findOrFail($data['payment_method_id']);
            $discountApplied = !empty($data['discount_id'])
                ? Discount::findOrFail($data['discount_id'])
                : null;

            if ($discountApplied && $discountApplied->quantity <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá đã hết lượt sử dụng.',
                ], 400);
            }
            Log::info($discountApplied);

            $order = Order::create([
                'sku' => Order::generateUniqueSku(),
                'user_id' => $user->id,
                'user_name' => $user->name,
                'shipping_name' => $data['fullname'],
                'shipping_phone' => $data['phone'],
                'shipping_address' => "{$data['address_detail']}, {$data['ward_name']}, {$data['district_name']}, {$data['province_name']}",
                'order_status' => 'Chưa xác nhận',
                'note' => $data['note'] ?? null,
                'payment_method_name' => $paymentMethod->name,
                'payment_status' => 'pending',
                'discount_value' => $discountApplied->discount_value ?? 0,
                'discount_type' => $discountApplied->discount_type ?? null,
                'discount_code' => $discountApplied->code ?? null,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'shipping_fee' => $data['shipping_fee'] ?? 0,
                'total_amount' => $data['final_total'],
            ]);

            foreach ($data['items'] as $item) {
                $productName = $item['product_variant']['product']['name'];
                $attribute = $item['product_variant']['attribute_name'] ?? '';
                $filename = Str::slug($productName . ' ' . $attribute) . '.jpg';

                $sourcePath = storage_path('app/public/' . $item['product_variant']['image']);
                $destinationPath = storage_path('app/public/images/orderItem/' . $filename);
                // Kiểm tra và sao chép hình ảnh sản phẩm
                if (File::exists($sourcePath) && File::isFile($sourcePath) && !File::exists($destinationPath)) {
                    File::ensureDirectoryExists(dirname($destinationPath));
                    File::copy($sourcePath, $destinationPath);
                }
                $productImagePath = 'images/orderItem/' . $filename;
                $totalPrice = $item['product_variant']['price'] * $item['quantity'] - ($item['_discount_amount'] ?? 0);

                // Kiểm tra tồn kho
                $variant = ProductVariant::with('product')->lockForUpdate()->findOrFail($item['product_variant']['id']);

                if ($variant->quantity < $item['quantity']) {
                    $productName = "{$variant->product->name} {$variant->attribute_name}";

                    $message = $variant->quantity <= 0
                        ? "Sản phẩm: '{$productName}' hiện đã hết hàng."
                        : "Sản phẩm: '{$productName}' chỉ còn {$variant->quantity} sản phẩm trong kho, không đủ cho yêu cầu mua {$item['quantity']} sản phẩm của bạn.";

                    // Log::info($message);

                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ]);
                }


                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $productName,
                    'product_variant_sku' => $item['product_variant']['sku'],
                    'product_image' => $productImagePath,
                    'product_attribute' => $attribute,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['product_variant']['price'],
                    'discount_amount' => $item['_discount_amount'] ?? 0,
                    'total_price' => $totalPrice,
                ]);
                // Trừ kho
                $variant->decrement('quantity', $item['quantity']);
            }
            if (!empty($data['discount_id'])) {
                DiscountUsage::create([
                    'discount_id' => $data['discount_id'],
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'user_name' => $user->name,
                    'discount_code' => $discountApplied->code,
                    'used_at' => now(),
                ]);
                $discountApplied->decrement('quantity');
            }
            $order->load(['items', 'user']);
            log::info('user: ' . $user);
            Mail::to($user->email)->queue(new OrderInvoiceMail($order, $user));


            DB::commit();
            log::info('payment method: ' . $paymentMethod->name);
            if (strtoupper($paymentMethod->name) === 'VNPAY') {
                CancelVnpayOrderJob::dispatch($order->id)->delay(now()->addHours(24));
                $redirectUrl = app(PaymentController::class)->createPaymentUrl($order);
                return response()->json([
                    'success' => true,
                    'redirect_url' => $redirectUrl,
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function list()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->with(['items.review'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('client.pages.viewOrder', compact('orders', 'user'));
    }

    public function show(Order $order)
    {
        $user = User::with('profile')
            ->find(Auth::id());
        // dd($user);

        if ($order->user_id !== $user->id) {
            return redirect()->route('orders.list')->with('error', 'Bạn không có quyền xem đơn hàng này.');
        }

        $order->load('items.review');

        return view('client.pages.invoice', compact('order', 'user'));
    }

    public function cancel($sku)
    {
        $order = Order::where('sku', $sku)->with('items')->firstOrFail();

        if ($order->user_id !== auth()->id()) {
            return redirect()->route('orders.list')->with('error', 'Bạn không có quyền hủy đơn hàng này.');
        }

        if (!$order->canBeCancel()) {
            return redirect()->route('orders.list')->with('error', 'Đơn hàng không thể hủy.');
        }

        try {
            DB::beginTransaction();

            // Cập nhật kho
            foreach ($order->items as $item) {
                if ($item->product_variant_sku) {
                    $variant = ProductVariant::where('sku', $item->product_variant_sku)->lockForUpdate()->first();
                    if ($variant) {
                        $variant->increment('quantity', $item->quantity);
                    }
                }
            }


            // Cập nhật trạng thái và lý do hủy
            $order->order_status = 'Hủy đơn';
            $order->cancel_reason = request('cancel_reason');
            $order->save();

            // xử lý mã giảm giá sau khi hủy đơn
            if ($order->discount_code) {
                $discount = Discount::where('code', $order->discount_code)->first();
                if ($discount) {
                    $discount->increment('quantity');

                    DiscountUsage::where('order_id', $order->id)->delete();
                }
            }

            DB::commit();

            return redirect()->route('orders.list')->with('success', 'Đơn hàng đã được hủy thành công.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Hủy đơn thất bại: ' . $e->getMessage());
            return redirect()->route('orders.list')->with('error', 'Có lỗi xảy ra khi hủy đơn hàng. Vui lòng thử lại.');
        }
    }

    public function submitReview(Request $request)
    {
        // 
        $data = $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'title' => 'required|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string',
            'images.*' => 'nullable|image|max:2048',
        ]);

        $orderItem = OrderItem::findOrFail($data['order_item_id']);
        $productVariant = ProductVariant::where('sku', $orderItem->product_variant_sku)->firstOrFail();

        $review = Review::create([
            'order_item_id' => $data['order_item_id'],
            'product_variant_id' => $productVariant->id,
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'rating' => $data['rating'],
            'content' => $data['content'],
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $image = $image->store('reviews', 'public');
                $review->images()->create(['image' => $image]);
            }
        }

        return redirect()->back()->with('success', 'Đánh giá đã được gửi thành công.');
    }
}
