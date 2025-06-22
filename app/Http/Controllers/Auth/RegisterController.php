<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\SendWelcomeEmailJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    protected function redirectTo()
    {
        return '/'; // Hoặc: return Auth::user()->role === 'admin' ? '/admin' : '/';
    }

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Hiển thị form đăng ký
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Xử lý đăng ký người dùng
     */
    public function register(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Vui lòng nhập tên.',
            'name.string' => 'Chỉ nhận ký tự chữ và số!',
            'name.max' => 'Không được quá 255 ký tự!',

            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại!',

            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu tối thiểu là 8 ký tự.',
            'password.confirmed' => 'Mật khẩu không trùng khớp.',
        ]);


        // Tạo người dùng
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Gửi email chào mừng bằng Job đưa vào hàng đợi
        dispatch(new SendWelcomeEmailJob($user->email, $user->name));

        // Tự động đăng nhập sau khi đăng ký
        Auth::login($user);

        return redirect($this->redirectTo());
    }
}
