<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            // Nếu chưa đăng nhập, redirect về route login admin
            return redirect()->route('admin.login');
        }

        if (auth()->user()->role !== 'admin') {
            return redirect()->route('admin.login');

        }

        return $next($request);
    }
}
