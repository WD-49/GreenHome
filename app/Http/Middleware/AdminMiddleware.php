<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Chưa đăng nhập
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        if (Auth::user()->role !== 'admin') {
            // Không phải admin
            Auth::logout(); // đề phòng client đang login
            return redirect()->route('login')->with('error', 'Bạn không có quyền truy cập');
        }

        return $next($request);
    }
}
