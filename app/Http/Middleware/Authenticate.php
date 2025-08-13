<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        if (!$request->expectsJson()) {
            if ($request->is('admin') || $request->is('admin/*')) { // Cái này check xem có phải là admin không
                return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
            }
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
    }
}
