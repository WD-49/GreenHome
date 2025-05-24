<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $title = 'dashboard';
        return view('admin.dashboard', compact('title'));
    }
}