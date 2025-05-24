<?php

namespace App\Http\Controllers\admin\Account;

use App\Models\User;
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
    // Lấy user kèm profile và comments
    $user = User::with(['profile', 'comments' => function ($q) {
        $q->withTrashed(); // nếu có soft deletes
    }])->findOrFail($id);

    return view('admin.account.users.detailAccUser', compact('user'));
}


    public function createUser()
    {
        return view('admin.account.users.createUser');
    }

    public function storeUser(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,client',
            'status' => 'required|boolean',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'gender' => 'required|in:nam,nu,khac',
            'user_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Tạo user
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->status = $request->status;
        $user->save();

        // Tạo user_profile
        $profile = new UserProfile();
        $profile->user_id = $user->id;
        $profile->phone = $request->phone;
        $profile->address = $request->address;
        $profile->gender = $request->gender;

        if ($request->hasFile('user_image')) {

            $image = $request->file('user_image');
            $filename = time() . '_' . Str::slug($user->name) . '.' . $image->getClientOriginalExtension();

            // Lưu ảnh mới
            $path = $image->storeAs('images/users', $filename, 'public');

            // Gán đường dẫn vào DB
            $profile->user_image = $path;
        }

        $profile->save();

        return redirect()->route('admin.account.listUsers')->with('success', 'Tạo người dùng thành công.');
    }

    public function editUser($id)
    {
        $user = User::with('profile')->findOrFail($id);
        return view('admin.account.users.editUser', compact('user'));
    }

    public function updateUser(Request $request, $id)
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

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->status = $request->status;
        $user->save();

        $profile = $user->profile ?? new UserProfile();
        $profile->user_id = $user->id;
        $profile->phone = $request->phone;
        $profile->address = $request->address;
        $profile->gender = $request->gender;

        if ($request->hasFile('user_image')) {
            // Xóa ảnh cũ nếu có
            if ($profile->user_image && Storage::disk('public')->exists($profile->user_image)) {
                Storage::disk('public')->delete($profile->user_image);
            }

            $image = $request->file('user_image');
            $filename = time() . '_' . Str::slug($user->name) . '.' . $image->getClientOriginalExtension();

            // Lưu ảnh mới
            $path = $image->storeAs('images/users', $filename, 'public');

            // Gán đường dẫn vào DB
            $profile->user_image = $path;
        }


        $profile->save();

        return redirect()->route('admin.account.listUsers')->with('success', 'Cập nhật người dùng thành công.');
    }


    public function softDeleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete(); // xóa mềm

        return redirect()->back()->with('success', 'Xóa người dùng thành công (soft delete).');
    }

    public function trashedUsers()
    {
        $trashedUsers =  User::onlyTrashed()->where('role', 'client')->with('profile')->paginate(10);
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
}
