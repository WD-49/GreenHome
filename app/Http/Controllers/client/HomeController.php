<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $title = 'Home';
        return view('client.home', compact('title'));
    }
}