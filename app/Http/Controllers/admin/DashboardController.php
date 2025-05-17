<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'dashboard';
        return view('admin.dashboard', compact('title'));
    }
}