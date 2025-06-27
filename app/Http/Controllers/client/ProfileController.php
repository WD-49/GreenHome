<?php

namespace App\Http\Controllers\client;

use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Review;
use App\Models\Comment;
use App\Models\Wishlist;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index($tab = 'info') // $tab vẫn được dùng để xác định tab nào active ban đầu
    {
        $user = Auth::user();
        dd($user);
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem trang cá nhân.');
        }

        $data = [];
        // Lấy tất cả dữ liệu cho TẤT CẢ CÁC TAB
        $data['profile'] = $user->profile;

        $data['orders'] = Order::where('user_id', $user->id)
            ->with('items.productVariant') // Tải trước các mục đơn hàng và biến thể sản phẩm
            ->orderBy('created_at', 'desc')
            ->get();

        $data['reviews'] = Review::where('user_id', $user->id)
            ->with('productVariant.product') // Tải trước biến thể sản phẩm và sản phẩm của nó
            ->orderBy('created_at', 'desc')
            ->get();

        $data['comments'] = Comment::where('user_id', $user->id)
            ->with('product') // Tải trước sản phẩm liên quan đến bình luận
            ->orderBy('created_at', 'desc')
            ->get();

        $cart = Cart::where('user_id', $user->id)
            ->with('items.productVariant.product') // Tải quan hệ
            ->first();
        if (!$cart) {
            $cart = new Cart([
                'user_id' => $user->id,
                'total_amount' => 0,
                'note' => null,
            ]);
            $cart->setRelation('items', new Collection()); // Tên quan hệ phải là 'items'
        }

        $data['cart'] = $cart;

        $data['wishlistItems'] = Wishlist::where('user_id', $user->id)
            ->with('product') // Tải trước sản phẩm
            ->orderBy('add_at', 'desc')
            ->get();
        // dd($data['orders']);
        // Trả về view chính của trang profile
        return view('client.pages.profile', compact('user', 'tab', 'data'));
    }

    // Phương thức update giữ nguyên như đã cung cấp trước đó
    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để chỉnh sửa thông tin.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('user_profiles')->ignore($user->profile->id ?? null, 'id')],
            'address' => 'nullable|string|max:255',
            'gender' => ['required', Rule::in(['nam', 'nu', 'khac'])],
            'birth_date' => 'nullable|date',
            'user_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        $profile = $user->profile ?: new UserProfile(['user_id' => $user->id]);
        $profile->phone = $request->input('phone');
        $profile->address = $request->input('address');
        $profile->gender = $request->input('gender');
        $profile->birth_date = $request->input('birth_date');

        if ($request->hasFile('user_image')) {
            if ($profile->user_image && file_exists(public_path($profile->user_image))) {
                unlink(public_path($profile->user_image));
            }
            $imageName = time().'.'.$request->user_image->extension();
            $request->user_image->move(public_path('images/users'), $imageName);
            $profile->user_image = 'images/users/' . $imageName;
        }

        $profile->save();

        return redirect()->route('profile.index', ['tab' => 'info'])->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
    }
}
