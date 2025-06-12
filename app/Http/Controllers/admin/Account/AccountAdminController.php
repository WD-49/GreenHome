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

class AccountAdminController extends Controller
{
    public function listAdmins(Request $request)
    {
        $query = User::with('profile')->where('role', 'admin');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('phone')) {
            $query->whereHas('profile', fn($q) => $q->where('phone', 'like', '%' . $request->phone . '%'));
        }

        if ($request->filled('address')) {
            $query->whereHas('profile', fn($q) => $q->where('address', 'like', '%' . $request->address . '%'));
        }

        if ($request->filled('gender')) {
            $query->whereHas('profile', fn($q) => $q->where('gender', $request->gender));
        }

        $admins = $query->paginate(10);
        // dd($admins);
        return view('admin.account.admin.listAdmins', compact('admins'));
    }

    public function detailAccAdmin($id)
    {
        $admins = User::with([
            'profile',
            'comments.product' => function ($query) {
                $query->withTrashed()->orderBy('created_at', 'desc');
            },
            'orders.items.product' => function ($query) {
                $query // Eager load quan hệ 'status' (trỏ đến OrderStatus)
                    ->orderBy('created_at', 'desc')
                    ->take(10);
            },
            // Cập nhật ở đây:
            'cartItems.productVariant.product' // Tải CartItem, rồi ProductVariant của nó, rồi Product của ProductVariant đó
        ])
            ->withCount(['orders', 'cartItems'])
            ->findOrFail($id);
        // dd($user);
        return view('admin.account.admin.detailAccAdmin', compact('admins'));
    }

    

    

    public function editAdmin($id)
    {
        $admins = User::with('profile')->findOrFail($id);
        return view('admin.account.admin.editAdmin', compact('admins'));
    }

    public function updateAdmin(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:client,admin',
            'status' => 'required|in:0,1',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'gender' => 'required|in:nam,nu,khac',
            'user_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $admins = User::findOrFail($id);
        $admins->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        $profile = $admins->profile ?? new UserProfile(['user_id' => $admins->id]);
        $profile->phone = $request->phone;
        $profile->address = $request->address;
        $profile->gender = $request->gender;

        if ($request->hasFile('user_image')) {
            // Xóa ảnh cũ nếu có
            if ($profile->user_image && Storage::disk('public')->exists($profile->user_image)) {
                Storage::disk('public')->delete($profile->user_image);
            }

            $image = $request->file('user_image');
            $filename = time() . '_' . Str::slug($admins->name) . '.' . $image->getClientOriginalExtension();

            // Lưu ảnh mới
            $path = $image->storeAs('images/users', $filename, 'public');

            // Gán đường dẫn vào DB
            $profile->user_image = $path;
        }

        $admins->profile()->save($profile);

        return redirect()->route('admin.account.listAdmins')->with('success', 'Cập nhật quản trị viên thành công.');
    }

    public function softDeleteAdmin($id)
    {
        $admins = User::findOrFail($id);
        $admins->delete();

        return redirect()->back()->with('success', 'Xóa quản trị viên thành công (soft delete).');
    }

    public function trashedAdmins()
    {
        $trashedAdmins = User::onlyTrashed()
            ->where('role', 'admin') // Chỉ lấy tài khoản admin
            ->with('profile')
            ->paginate(10);

        return view('admin.account.admin.trashedAdmins', compact('trashedAdmins'));
    }

    public function restoreAdmin($id)
    {
        $admin = User::withTrashed()->findOrFail($id);
        $admin->restore();

        return redirect()->back()->with('success', 'Khôi phục quản trị viên thành công.');
    }

    public function forceDeleteAdmin($id)
    {
        $admin = User::withTrashed()->findOrFail($id);

        if ($admin->profile) {
            $profile = $admin->profile;

            // Xóa ảnh cũ nếu có
            if ($profile->user_image && Storage::disk('public')->exists($profile->user_image)) {
                Storage::disk('public')->delete($profile->user_image);
            }
            // dd($profile->user_image);

            // Xóa luôn profile (có thể dùng forceDelete nếu có soft deletes)
            $profile->delete(); // hoặc $profile->forceDelete(); nếu model có SoftDeletes
        }

        $admin->forceDelete();

        return redirect()->back()->with('success', 'Xóa quản trị viên vĩnh viễn thành công.');
    }

    public function resetPassAdmin($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $newPassword = 'greenhome';
        $admin->password = Hash::make($newPassword);
        $admin->save();

        return redirect()->back()->with('success', "Đặt lại mật khẩu thành công. Mật khẩu mới: $newPassword");
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
