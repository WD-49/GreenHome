<?php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers\client;

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
            $cart->total_amount = $cart->items()->sum('total_price');
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
}
