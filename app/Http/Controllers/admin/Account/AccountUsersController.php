<?php

namespace App\Http\Controllers\admin\Account;

use App\Models\User;
use App\Models\Order;
use App\Models\UserProfile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountUsersController extends Controller
{
    public function listUsers(Request $request)
    {
        $query = User::with('profile') // Eager load profile để tránh N+1
            ->where('role', 'client');   // Lọc role là 'client'

        // Lọc theo name (từ bảng users)
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Lọc theo email (từ bảng users)
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        // Lọc theo phone (từ bảng user_profiles)
        if ($request->filled('phone')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->phone . '%');
            });
        }

        // Lọc theo address (từ bảng user_profiles)
        if ($request->filled('address')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('address', 'like', '%' . $request->address . '%');
            });
        }

        // Lọc theo gender (từ bảng user_profiles)
        if ($request->filled('gender')) {
            $query->whereHas('profile', function ($q) use ($request) {
                $q->where('gender', $request->gender);
            });
        }

        $users = $query->paginate(10); // Phân trang 10 dòng mỗi trang
        // dd($Users);
        return view('admin.account.users.listUsers', compact('users'));
    }

    public function detailAccUser($id)
    {
        $user = User::with([
            'profile',
            'comments' => function ($query) {
                $query->withTrashed()->orderBy('created_at', 'desc');
            },
            'orders' => function ($query) {
                $query->with('status') // Eager load quan hệ 'status' (trỏ đến OrderStatus)
                    ->orderBy('created_at', 'desc')
                    ->take(10);
            },
            // Cập nhật ở đây:
            'cartItems.productVariant.product' // Tải CartItem, rồi ProductVariant của nó, rồi Product của ProductVariant đó
        ])
            ->withCount(['orders', 'cartItems'])
            ->findOrFail($id);
        // dd($user);
        return view('admin.account.users.detailAccUser', compact('user'));
    }


    public function softDeleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // xóa mềm

        return redirect()->back()->with('success', 'Xóa người dùng thành công (soft delete).');
    }

    public function trashedUsers()
    {
        $trashedUsers = User::onlyTrashed()->where('role', 'client')->with('profile')->paginate(10);
        return view('admin.account.users.trashedUsers', compact('trashedUsers'));
    }

    public function trashedAdmins()
    {
        $trashedAdmins = User::onlyTrashed()
            ->where('role', 'admin') // Chỉ lấy tài khoản admin
            ->with('profile')
            ->paginate(10);

        return view('admin.account.admin.trashedAdmins', compact('trashedAdmins'));
    }

    public function restoreUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->back()->with('success', 'Khôi phục người dùng thành công.');
    }

    public function forceDeleteUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        // Nếu có profile
        if ($user->profile) {
            $profile = $user->profile;

            // Xóa ảnh cũ nếu có
            if ($profile->user_image && Storage::disk('public')->exists($profile->user_image)) {
                Storage::disk('public')->delete($profile->user_image);
            }
            // dd($profile->user_image);

            // Xóa luôn profile (có thể dùng forceDelete nếu có soft deletes)
            $profile->delete(); // hoặc $profile->forceDelete(); nếu model có SoftDeletes
        }

        // Xóa user vĩnh viễn
        $user->forceDelete();

        return redirect()->back()->with('success', 'Xóa người dùng vĩnh viễn thành công.');
    }

    public function resetPassUser($id)
    {
        $user = User::where('role', 'client')->findOrFail($id); // chỉ chọn user thường

        // Tạo mật khẩu random
        $newPassword = Str::random(8);

        $user->password = Hash::make($newPassword);
        $user->save();

        return redirect()->back()->with('success', "Đặt lại mật khẩu thành công cho user. Mật khẩu mới: $newPassword");
    }

    public function getAjaxOrderDetails(Request $request, Order $order)
    {
        if (!$request->ajax()) {
            return abort(403, 'Truy cập không hợp lệ.');
        }

        // Eager load các thông tin cần thiết
        // Bỏ 'shippingAddress...' vì nó không phải là relationship
        $order->load([
            'user:id,name,email',       // Thông tin người đặt hàng (nếu order có user_id)
            'status',                   // Trạng thái đơn hàng (từ model OrderStatus)
            'items.productVariant.product', // Đảm bảo 'items' và 'images' là các relationship ĐÚNG
        ]);

        // Xử lý dữ liệu order items
        $orderItemsData = $order->items->map(function ($item) { // Đảm bảo relationship tên là 'items' trong Order model
            $product = optional(optional($item->productVariant)->product);
            $variant = $item->productVariant;
            $productName = $product->name ?? 'Sản phẩm không xác định';

            $variantAttributes = [];
            if ($variant && $variant->attributes) {
                $attributesArray = is_string($variant->attributes) ? json_decode($variant->attributes, true) : (array) $variant->attributes;
                if (is_array($attributesArray)) {
                    foreach ($attributesArray as $attribute) {
                        if (is_array($attribute) && isset($attribute['value'])) {
                            $variantAttributes[] = $attribute['value'];
                        }
                    }
                }
            }
            if (!empty($variantAttributes)) {
                $productName .= ' (' . implode(' - ', $variantAttributes) . ')';
            }

            $imageUrl = 'https://placehold.co/60x60/EBF0F5/7F8EA3?text=Ảnh+SP';
            if ($product->exists) {
                if ($product->images && $product->images->isNotEmpty()) { // Nếu 'images' là collection
                    $imgPath = $product->images->first()->image_url;
                    if ($imgPath) {
                        $imageUrl = str_starts_with($imgPath, 'http') ? $imgPath : Storage::url($imgPath);
                    }
                } elseif ($product->image) { // Nếu 'image' là một trường
                    $imgPath = $product->image;
                    $imageUrl = str_starts_with($imgPath, 'http') ? $imgPath : Storage::url($imgPath);
                }
            }

            return [
                'id' => $item->id,
                'product_name' => $productName,
                'quantity' => $item->quantity,
                'unit_price' => number_format($item->unit_price, 0, ',', '.') . ' VNĐ',
                'sub_total' => number_format($item->quantity * $item->unit_price, 0, ',', '.') . ' VNĐ',
                'image_url' => $imageUrl,
            ];
        });

        // Lấy thông tin giao hàng trực tiếp từ các cột của bảng orders
        $shippingAddressData = [
            'name' => $order->shipping_name ?? 'N/A',
            'phone' => $order->shipping_phone ?? 'N/A',
            'address_line1' => $order->shipping_address ?? 'N/A',
            // Không có ward, district, city riêng cho shipping trong bảng orders
            // Nếu bạn muốn lấy từ user_profiles, bạn cần có user_id trên order và load profile của user đó
            'ward' => null,
            'district' => null,
            'city' => null,
        ];

        // Nếu bạn muốn thử lấy thông tin phường/xã/thành phố từ user_profile của người đặt hàng (nếu có)
        // bạn cần đảm bảo $order->user và $order->user->profile được load
        // Ví dụ:
        // if ($order->user && $order->user->profile) {
        //     $shippingAddressData['ward'] = $order->user->profile->ward;
        //     $shippingAddressData['district'] = $order->user->profile->district;
        //     $shippingAddressData['city'] = $order->user->profile->city;
        // }


        // CHUYỂN ĐỔI TRẠNG THÁI THANH TOÁN
        $paymentStatusDisplay = $order->payment_status; // Giữ giá trị gốc
        $paymentStatusClass = 'bg-warning text-dark'; // Mặc định cho pending

        switch (strtolower($order->payment_status)) {
            case 'pending':
                $paymentStatusDisplay = 'Đang chờ';
                $paymentStatusClass = 'bg-warning text-dark';
                break;
            case 'paid':
                $paymentStatusDisplay = 'Đã thanh toán';
                $paymentStatusClass = 'bg-success';
                break;
            case 'failed':
                $paymentStatusDisplay = 'Không thành công';
                $paymentStatusClass = 'bg-danger';
                break;
            default:
                $paymentStatusDisplay = ucfirst($order->payment_status ?? 'N/A');
                // Giữ class mặc định hoặc thêm logic nếu có các trạng thái khác
                break;
        }

        return response()->json([
            'success' => true,
            'order' => [
                'id' => $order->id,
                'sku' => $order->sku ?? $order->id,
                'created_at_formatted' => $order->created_at->format('d/m/Y H:i:s'),
                'status_name' => optional($order->status)->name ?? ucfirst($order->status ?? 'N/A'),
                'status_color_class' => optional($order->status)->color_class ?? 'bg-secondary',
                'payment_method' => ucfirst(str_replace('_', ' ', optional($order->paymentMethod)->name ?? $order->payment_method ?? 'N/A')),
                'payment_status_original' => $order->payment_status, // Giữ lại giá trị gốc nếu cần
                'payment_status_display' => $paymentStatusDisplay,    // Giá trị đã dịch
                'payment_status_class' => $paymentStatusClass,        // Class cho badge
                'shipping_fee' => number_format($order->shipping_fee ?? 0, 0, ',', '.') . ' VNĐ',
                'discount_amount' => number_format($order->discount_amount ?? 0, 0, ',', '.') . ' VNĐ',
                'total_amount' => number_format($order->total_amount, 0, ',', '.') . ' VNĐ',
                'notes' => $order->note,
            ],
            'order_items' => $orderItemsData,
            'shipping_address' => $shippingAddressData,
            'customer' => [
                'name' => optional($order->user)->name ?? ($order->shipping_name ?? 'Khách vãng lai'),
                'email' => optional($order->user)->email ?? 'N/A',
            ]
        ]);
    }
}
