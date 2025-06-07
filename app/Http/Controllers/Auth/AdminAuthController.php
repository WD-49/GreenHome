<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        // dd(Auth::user());
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.adminLogin');
    }

    public function login(Request $request)
    {
        // dd('check');
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
        ], [
            'email.required' => 'Email là bắt buộc',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Mật khẩu là bắt buộc',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);
        // dd($remember);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');


        if (Auth::attempt($credentials, $remember)) {
            if (Auth::user()->role === 'admin') {
                $request->session()->regenerate();
                Auth::user()->load('profile');
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Đăng nhập thành công!');
            } else {
                Auth::logout();
                return back()->with('error', 'Bạn không có quyền truy cập trang admin');
            }
        }

        return back()->with('error', 'Email hoặc mật khẩu không chính xác');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Đăng xuất thành công');
    }
}
