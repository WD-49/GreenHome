<?php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers\client;

use Log;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function getCartData()
    {
        $cart = null;

        $cart = Cart::where(function ($q) {
            $q->where('user_id', Auth::user()->id);
        })
            ->with(['items.productVariant.product']) // eager load
            ->first();
        // Trả về dữ liệu giỏ hàng
        return response()->json([
            'success' => true,
            'cart' => $cart
        ]);
    }
    public function addToCart(Request $request)
    {
        try {
            $request->validate([
                'product_variant_id' => 'required|exists:product_variants,id',
                'quantity' => 'required|integer|min:1'
            ]);

            $user = Auth::user();
            $variant = ProductVariant::findOrFail($request->product_variant_id);

            // Tìm hoặc tạo cart cho user
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id, 'deleted_at' => null],
                ['total_amount' => 0]
            );

            // Kiểm tra nếu đã có item này trong cart thì tăng số lượng
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_variant_id', $variant->id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity += $request->quantity;
                $cartItem->total_price = $cartItem->quantity * $variant->price;
                $cartItem->save();
            } else {
                $cartItem = CartItem::create([
                    'cart_id' => $cart->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $request->quantity,
                    'unit_price' => $variant->price,
                    'total_price' => $request->quantity * $variant->price,
                ]);
            }

            // Cập nhật tổng tiền cart
            $cart->save();


            $cart = $this->getCartData();

            return response()->json(
                ['success' => true, 'message' => 'Đã thêm vào giỏ hàng!', 'cart' => $cart],
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function viewCart(Request $request)
    {
        return view('client.pages.viewCart');
    }

    public function updateQuantity(Request $request, $id)
    {
        $quantity = $request->input('quantity');
        $cartItem = CartItem::findOrFail($id);
        $total_price = $cartItem->unit_price * $quantity;
        $cartItem->quantity = $quantity;
        $cartItem->total_price = $total_price;
        $cartItem->save();

        return response()->json(['success' => true]);
    }

    public function deleteMultiple(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Chưa đăng nhập']);
        }

        $ids = $request->input('ids');

        if (!is_array($ids) || empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ!',
            ]);
        }

        // Lấy danh sách cart_id của user hiện tại
        $cartIds = Cart::where('user_id', Auth::id())->pluck('id')->toArray();

        if (empty($cartIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy giỏ hàng của người dùng.',
            ]);
        }

        // Lấy cart_id các cart_items sẽ bị xóa
        $affectedCartIds = CartItem::whereIn('id', $ids)
            ->whereIn('cart_id', $cartIds)
            ->pluck('cart_id')
            ->unique()
            ->toArray();

        // Xóa cứng các cart_items có id nằm trong danh sách và cart_id thuộc user đó
        $deleted = CartItem::whereIn('id', $ids)
            ->whereIn('cart_id', $cartIds)
            ->forceDelete();

        foreach ($affectedCartIds as $cartId) {
            $cart = Cart::find($cartId);
            if (!$cart) continue;

            $totalAmount = $cart->items()->sum('total_price') ?? 0;

            // Cập nhật total_amount
            $cart->total_amount = $totalAmount;
            $cart->save();
        }

        return response()->json([
            'success' => $deleted > 0,
            'message' => $deleted > 0 ? null : 'Không thể xoá sản phẩm!',
        ]);
    }
}
