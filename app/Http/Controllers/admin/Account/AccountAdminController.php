<?php

namespace App\Http\Controllers\Admin\Account;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

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
        return view('admin.account.admin.listAdmins', compact('admins'));
    }

    public function detailAccAdmin($id)
    {
        $admins = User::with('profile')->findOrFail($id);
        return view('admin.account.admin.detailAccAdmin', compact('admins'));
    }

    public function createAdmin()
    {
        return view('admin.account.admin.createAdmin');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,user',
            'status' => 'required|boolean',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'gender' => 'required|in:male,female',
            'user_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $admins = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'status'   => $request->status,
        ]);

        $profile = new UserProfile([
            'phone'   => $request->phone,
            'address' => $request->address,
            'gender'  => $request->gender,
        ]);

        if ($request->hasFile('user_image')) {
            $image = $request->file('user_image');
            $filename = time() . '_' . Str::slug($admins->name) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/avatars'), $filename);
            $profile->user_image = 'uploads/avatars/' . $filename;
        }

        $admins->profile()->save($profile);

        return redirect()->route('admin.account.listAdmins')->with('success', 'Tạo quản trị viên thành công.');
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
            'role' => 'required|in:user,admin',
            'status' => 'required|in:0,1',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'user_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $admins = User::findOrFail($id);
        $admins->update([
            'name'   => $request->name,
            'email'  => $request->email,
            'role'   => $request->role,
            'status' => $request->status,
        ]);

        $profile = $admins->profile ?? new UserProfile(['user_id' => $admins->id]);
        $profile->phone = $request->phone;
        $profile->address = $request->address;
        $profile->gender = $request->gender;

        if ($request->hasFile('user_image')) {
            $image = $request->file('user_image');
            $filename = time() . '_' . Str::slug($admins->name) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/avatars'), $filename);
            $profile->user_image = 'uploads/avatars/' . $filename;
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
        $trashedAdmins = User::onlyTrashed()->with('profile')->paginate(10);
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
            $admin->profile->delete();
        }

        $admin->forceDelete();

        return redirect()->back()->with('success', 'Xóa quản trị viên vĩnh viễn thành công.');
    }

    public function resetPassword($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $newPassword = Str::random(8);
        $admin->password = Hash::make($newPassword);
        $admin->save();

        return redirect()->back()->with('success', "Đặt lại mật khẩu thành công. Mật khẩu mới: $newPassword");
    }
}
