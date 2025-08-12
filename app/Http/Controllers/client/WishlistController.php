<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WishList;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WishlistController extends Controller

{
    public function __construct()
    {
        $this->middleware('auth')->only(['add', 'remove', 'index']);
    }


    public function index()
    {
        $wishlists = WishList::with('product')
            ->where('user_id', Auth::id())
            ->orderByRaw("FIELD(priority, 'High', 'Medium', 'Low')") // ← sắp theo priority
            ->orderByDesc('id') // ← ưu tiên mới nhất nếu trùng priority
            ->paginate(12);

        return view('client.pages.wishlist', compact('wishlists'));
    }


    public function add(Request $request)
    {

        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        WishList::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $request->product_id],
            [
                'add_at' => Carbon::now(),
                'notify_on_sale' => false,
                'priority' => 'Medium'
            ]
        );

        return response()->json(['success' => true, 'message' => 'Đã thêm vào wishlist']);
    }

    public function remove(Request $request)
    {
        $wishlist = WishList::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($wishlist) {
            $wishlist->forceDelete(); // ← Xóa vĩnh viễn
        }

        return response()->json(['success' => true, 'message' => 'Đã xóa khỏi wishlist']);
    }


    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Bạn chưa đăng nhập'], 401);
        }

        $userId = Auth::id();
        $productId = $request->input('product_id');

        $existing = WishList::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'message' => 'Đã xóa khỏi wishlist!',
                'added' => false,
            ]);
        }

        WishList::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'add_at' => now(),
            'notify_on_sale' => false,
            'priority' => 'Medium',
        ]);

        return response()->json([
            'message' => 'Đã thêm vào wishlist!',
            'added' => true,
        ]);
    }

    public function updateOptions(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'field' => 'required|in:notify_on_sale,priority',
            'value' => 'required'
        ]);

        $wishlist = WishList::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->firstOrFail();

        // Cập nhật field phù hợp
        $wishlist->{$request->field} = $request->value;
        $wishlist->save();

        return response()->json(['message' => 'Đã cập nhật thành công!']);
    }
}
