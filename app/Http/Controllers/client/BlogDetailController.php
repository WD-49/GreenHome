<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;

class BlogDetailController extends Controller
{
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        // dd(asset('storage/' . $blog->thumbnail));


        return view('client.pages.blogDetail', compact('blog'));
    }
}
