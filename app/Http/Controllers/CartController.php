<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\ProductVariant;
use App\Models\Cart;

class CartController extends Controller
{
    /**
     * Hiển thị giỏ hàng nhỏ (mini cart)
     */

    public function index()
    {
        $user = Auth::user();
        $items = collect(); // Tạo collection rỗng mặc định

        if ($user) {
            $cartItems = CartItem::with(['productVariant.product'])
                ->where('user_id', $user->id)
                ->get();

            $items = $cartItems->map(function ($item) {
                $variant = $item->productVariant;
                $product = $variant->product ?? null;

                return [
                    'id' => $item->id,
                    'slug' => $product?->slug,
                    'name' => $product?->name ?? 'Sản phẩm',
                    'image' => $variant?->image ?? $product?->image ?? 'default.jpg',
                    'price' => $item->unit_price ?? $variant?->price ?? 0,
                    'quantity' => $item->quantity ?? 1,
                ];
            });
        } else {
            $sessionCart = session()->get('cart', []);
            $items = collect($sessionCart)->map(function ($item) {
                return [
                    'id' => $item['variant_id'],
                    'slug' => $item['slug'] ?? null,
                    'name' => $item['name'] ?? 'Sản phẩm',
                    'image' => $item['image'] ?? 'default.jpg',
                    'price' => $item['price'] ?? 0,
                    'quantity' => $item['quantity'] ?? 1,
                ];
            });
        }

        // Tính tổng
        $subtotal = $items->sum(fn($i) => $i['price'] * $i['quantity']);
        $vat = $subtotal * 0.2;
        $total = $subtotal + $vat;

        $vouchers = Discount::where('status', 'active')->get();
        dd($items);

        return view('client.partials.miniCart', compact('items', 'subtotal', 'vat', 'total', 'vouchers'));
    }


    /**
     * Thêm vào giỏ hàng (dùng DB hoặc session)
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::with('product')->findOrFail($request->product_variant_id);

        if (Auth::check()) {
            $user = Auth::user();

            // Tạo cart nếu chưa có
            $cart = $user->cart ?? Cart::create(['user_id' => $user->id]);

            CartItem::updateOrCreate(
                [
                    'cart_id' => $cart->id,
                    'product_variant_id' => $variant->id,
                ],
                ['quantity' => $request->quantity]
            );
        } else {
            // Session cho guest
            $cart = session()->get('cart', []);
            $variantId = $variant->id;

            if (isset($cart[$variantId])) {
                $cart[$variantId]['quantity'] += $request->quantity;
            } else {
                $cart[$variantId] = [
                    'product_id' => $variant->product->id,
                    'variant_id' => $variantId,
                    'slug' => $variant->product->slug,
                    'name' => $variant->product->name,
                    'image' => $variant->image,
                    'price' => $variant->price,
                    'quantity' => $request->quantity,
                ];
            }

            session()->put('cart', $cart);
        }

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Đã thêm vào giỏ hàng',
        // ]);
        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng',
            'slug' => $variant->product->slug,
        ]);
    }

    /**
     * Xóa 1 item khỏi giỏ hàng
     */
    public function remove($id)
    {
        if (Auth::check()) {
            CartItem::destroy($id);
        } else {
            $cart = session()->get('cart', []);
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Đã xóa khỏi giỏ hàng.');
    }

    /**
     * Trang thanh toán
     */

    public function checkout()
    {
        $user = Auth::user();
        $items = collect();

        if ($user) {
            $cartItems = CartItem::with(['productVariant.product'])
                ->where('user_id', $user->id)
                ->get();

            $items = $cartItems->map(function ($item) {
                $variant = $item->productVariant;
                $product = $variant?->product;

                return [
                    'id' => $item->id,
                    'slug' => $product?->slug,
                    'name' => $product?->name ?? 'Sản phẩm',
                    'image' => $variant?->image ?? $product?->image ?? 'default.jpg',
                    'price' => $item->unit_price ?? $variant?->price ?? 0,
                    'quantity' => $item->quantity ?? 1,
                ];
            });
        } else {
            $sessionCart = session()->get('cart', []);
            $items = collect($sessionCart)->map(function ($item) {
                return [
                    'id' => $item['variant_id'],
                    'slug' => $item['slug'] ?? null,
                    'name' => $item['name'] ?? 'Sản phẩm',
                    'image' => $item['image'] ?? 'default.jpg',
                    'price' => $item['price'] ?? 0,
                    'quantity' => $item['quantity'] ?? 1,
                ];
            });
        }

        $subtotal = $items->sum(fn($i) => $i['price'] * $i['quantity']);
        $vat = $subtotal * 0.2;
        $total = $subtotal + $vat;

        $vouchers = Discount::where('status', 'active')->get();

        return view('client.pages.checkout', compact('items', 'subtotal', 'vat', 'total', 'vouchers'));
    }

    /**
     * Hiển thị popup hoặc trang voucher
     */
    public function show()
    {
        $vouchers = Discount::where('status', 'active')->get();
        return view('client.pages.voucher', compact('vouchers'));
    }


    public function showMiniCart()
    {
        $user = Auth::user();

        // Tự động tạo giỏ hàng nếu chưa có
        $cart = $user->cart ?: Cart::create(['user_id' => $user->id]);

        // Lấy danh sách cart item kèm variant & product
        $items = CartItem::with(['productVariant.product'])
            ->where('cart_id', $cart->id)
            ->get();

        $subtotal = $items->sum(fn($item) => $item->unit_price * $item->quantity);
        $vat = $subtotal * 0.2;
        $total = $subtotal + $vat;

        return view('client.partials.miniCart', compact('items', 'subtotal', 'vat', 'total', 'vouchers'));
    }

    public function updateQuantity(Request $request)
    {
        $item = CartItem::findOrFail($request->id);
        $item->quantity = $request->quantity;
        $item->save();

        return response()->json(['success' => true]);
    }

    public function removeItem($id)
    {
        CartItem::destroy($id);
        return response()->json(['success' => true]);
    }
}
