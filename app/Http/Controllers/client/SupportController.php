<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function index() {
        $faqs = Faq::paginate(10);
        return view('client.pages.support', compact('faqs'));
    }
}
