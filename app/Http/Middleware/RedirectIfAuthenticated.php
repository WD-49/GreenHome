<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $guard = $guards[0] ?? null;

        if (Auth::guard($guard)->check()) {
            // Nếu người dùng đã đăng nhập, redirect về trang phù hợp
            $role = Auth::user()->role ?? null;

            return match ($role) {
                'admin' => redirect('/admin'),
                'client' => redirect('/'),
                default => redirect('/'),
            };
        }

        return $next($request);
    }
}
