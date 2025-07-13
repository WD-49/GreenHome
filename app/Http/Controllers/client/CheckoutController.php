<?php

namespace App\Http\Controllers\client;

use App\Models\Cart;
use App\Models\Order;
use App\Models\WebInfo;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Models\PaymentMethod;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

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
                'province' => 'required|string',
                'district' => 'required|string',
                'ward' => 'required|string',
                'address_detail' => 'required|string',
                'note' => 'nullable|string',
                'payment_method_id' => 'required|exists:payment_methods,id',
                'final_total' => 'required|numeric',
                'discount_id' => 'nullable|exists:discounts,id',
                'discount_amount' => 'nullable|numeric',
                'items' => 'required|array|min:1',
            ]);

            DB::beginTransaction();

            $paymentMethod = PaymentMethod::findOrFail($data['payment_method_id']);
            dd($data['discount_code']);

            $order = Order::create([
                'sku' => Order::generateUniqueSku(),
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'shipping_name' => $data['fullname'],
                'shipping_phone' => $data['phone'],
                'shipping_address' => "{$data['address_detail']}, {$data['ward']}, {$data['district']}, {$data['province']}",
                'order_status' => 'Chưa xác nhận',
                'note' => $data['note'] ?? null,
                'payment_method_id' => $data['payment_method_id'],
                'payment_method_name' => $paymentMethod->name,
                'payment_status' => 'pending',
                'discount_id' => $data['discount_id'],
                'discount_value' => $data['discount_value'] ?? 0,
                'discount_code' => $data['discount_code'] ?? null,
                'discount_amount' => $data['discount_amount'] ?? 0,
                'shipping_fee' => $data['shipping_fee'] ?? 0,
                'total_amount' => $data['final_total'],
            ]);

            foreach ($data['items'] as $item) {
                $totalPrice = $item['product_variant']['price'] * $item['quantity'] - ($item['_discount_amount'] ?? 0);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item['product_variant']['id'],
                    'product_name' => $item['product_variant']['product']['name'],
                    'product_variant_sku' => $item['product_variant']['sku'],
                    'product_attribute' => $item['product_variant']['attribute_name'] ?? '',
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['product_variant']['price'],
                    'discount_amount' => $item['_discount_amount'] ?? 0,
                    'total_price' => $totalPrice,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
