<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */

    // Hàm này sẽ chuyển hướng người dùng đến trang xác thực của Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */

    // Hàm này sẽ xử lý callback từ Google sau khi người dùng xác thực
    public function handleGoogleCallback()
    {
        try {
            $socialiteUser = Socialite::driver('google')->user();

            $user = User::where('email', $socialiteUser->getEmail())->first();

            if ($user) {
                // User already exists, log them in
                Auth::login($user);
            } else {
                // User doesn't exist, create new user and log them in
                $newUser = User::create([
                    'name' => $socialiteUser->getName(),
                    'email' => $socialiteUser->getEmail(),
                    'password' => bcrypt(uniqid()), // Create a random password
                    'role' => 'client',
                    'status' => 1,
                ]);

                Auth::login($newUser);
            }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Chào mừng bạn trở lại, Admin!');
            }

            return redirect('/')->with('success', 'Đăng nhập thành công!');
        } catch (\Exception $e) {
            Log::error('Google login failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Đăng nhập bằng Google thất bại. Vui lòng thử lại.');
        }
    }

    /**
     * Redirect the user to the Facebook authentication page.
     */
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     */
    public function handleFacebookCallback()
    {
        try {
            $socialiteUser = Socialite::driver('facebook')->user();

            $user = User::where('email', $socialiteUser->getEmail())->first();

            if ($user) {
                // User already exists, log them in
                Auth::login($user);
            } else {
                // User doesn't exist, create new user and log them in
                $newUser = User::create([
                    'name' => $socialiteUser->getName(),
                    'email' => $socialiteUser->getEmail(),
                    'password' => bcrypt(uniqid()), // Create a random password
                    'role' => 'user',
                    'status' => 1,
                ]);

                Auth::login($newUser);
            }

            return redirect()->intended('/')->with('success', 'Đăng nhập bằng Facebook thành công!');
        } catch (\Exception $e) {
            Log::error('Facebook login failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Đăng nhập bằng Facebook thất bại. Vui lòng thử lại.');
        }
    }
}
