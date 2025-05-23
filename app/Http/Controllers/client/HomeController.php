<?php
namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $title = 'Home';
        return view('client.home', compact('title'));
    }
}