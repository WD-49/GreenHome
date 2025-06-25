<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Jobs\SendResetPasswordMailJob;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function handle(Request $request)
    {
        $request->validate(['email' => 'required|email']);


        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email không tồn tại.']);
        }

        // Tạo mật khẩu mới
        $newPassword = Str::random(8);
        $user->password = Hash::make($newPassword);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Gửi email vào queue
        dispatch(new SendResetPasswordMailJob($user->email, $newPassword));

        return back()->with('status', 'Mật khẩu mới đã được gửi qua email!');
    }
}
