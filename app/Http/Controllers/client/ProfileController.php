<?php

namespace App\Http\Controllers\Client;

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
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function index($tab = 'info') // $tab vẫn được dùng để xác định tab nào active ban đầu
    {
        $user = Auth::user();
        // dd($user);
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem trang cá nhân.');
        }

        $data = [];
        // Lấy tất cả dữ liệu cho TẤT CẢ CÁC TAB
        $data['profile'] = $user->profile;

        $data['orders'] = Order::where('user_id', $user->id)
            ->with('items') // Tải trước các mục đơn hàng và biến thể sản phẩm
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $data['reviews'] = Review::where('user_id', $user->id)
             // Tải trước biến thể sản phẩm và sản phẩm của nó
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $data['comments'] = Comment::where('user_id', $user->id)
            ->with('product') // Tải trước sản phẩm liên quan đến bình luận
            ->orderBy('created_at', 'desc')
            ->paginate(5);

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
            ->paginate(5);
        // dd($data['orders']);
        // Trả về view chính của trang profile
        return view('client.pages.profile', compact('user', 'tab', 'data'));
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để chỉnh sửa thông tin.');
        }

        // Tạo Validator thủ công
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20', Rule::unique('user_profiles')->ignore($user->profile->id ?? null, 'id')],
            'address' => 'nullable|string|max:255',
            'gender' => ['required', Rule::in(['nam', 'nu', 'khac'])],
            'birth_date' => 'nullable|date',
            'user_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        // KIỂM TRA LỖI VALIDATION VÀ CHUYỂN HƯỚNG VỚI LỖI
        if ($validator->fails()) {
            return redirect()->back() // Chuyển hướng về trang trước đó
                ->withErrors($validator) // Đính kèm các lỗi vào session
                ->withInput(); // Giữ lại dữ liệu đã nhập vào form
        }

        // Nếu validation thành công, code sẽ chạy tiếp từ đây
        try {
            $user->name = $request->input('name');
            // Nếu email bị thay đổi, đặt lại email_verified_at thành null
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            $user->email = $request->input('email');
            $user->save();

            $profile = $user->profile ?: new UserProfile(['user_id' => $user->id]);
            $profile->phone = $request->input('phone');
            $profile->address = $request->input('address');
            $profile->gender = $request->input('gender');
            $profile->birth_date = $request->input('birth_date');
           
            if ($request->hasFile('user_image')) {
                // Xóa ảnh cũ (nếu có và nếu nó được lưu theo cách CŨ hoặc theo cách MỚI)
                // Nếu ảnh cũ lưu trong public_path:
                if ($profile->user_image && file_exists(public_path($profile->user_image))) {
                    unlink(public_path($profile->user_image));
                }
                // Nếu ảnh cũ lưu trong storage/app/public (cách chuẩn):
                if ($profile->user_image && Storage::disk('public')->exists($profile->user_image)) {
                    Storage::disk('public')->delete($profile->user_image);
                }

                $imagePath = $request->file('user_image')->store('images/users', 'public'); // Lưu vào storage/app/public/images/users
                $profile->user_image = $imagePath; // Đường dẫn trong DB sẽ là 'images/users/ten_file.png'
            }

            $profile->save();

            return redirect()->route('profile.index', ['tab' => 'info'])->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
        } catch (\Exception $e) {
            // Log lỗi hoặc hiển thị thông báo lỗi chung
            Log::error('Update Profile Error: ' . $e->getMessage(), ['user_id' => $user->id, 'request' => $request->all()]);
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật thông tin: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // 1. Xác thực dữ liệu đầu vào
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // 'confirmed' sẽ tự động so khớp với new_password_confirmation
        ], [
            'current_password.required' => 'Mật khẩu hiện tại không được để trống.',
            'new_password.required' => 'Mật khẩu mới không được để trống.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        // 2. Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password)) {
            // Log lỗi để debug nếu cần
            Log::warning('Lỗi đổi mật khẩu: Mật khẩu hiện tại không đúng.', [
                'user_id' => $user->id,
                'input_current_password' => $request->current_password, // Không nên log mật khẩu thật trong môi trường production
            ]);
            throw ValidationException::withMessages([
                'current_password' => ['Mật khẩu hiện tại không đúng.'],
            ]);
        }

        // 3. Cập nhật mật khẩu mới
        try {
            $user->password = Hash::make($request->new_password);
            $user->save();

            return redirect()->route('profile.index', ['tab' => 'password'])->with('success', 'Mật khẩu của bạn đã được cập nhật thành công!');
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật mật khẩu: ' . $e->getMessage(), ['user_id' => $user->id]);
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra khi cập nhật mật khẩu: ' . $e->getMessage());
        }
    }
}
