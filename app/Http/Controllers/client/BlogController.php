<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index($slugCategory = null)
    {
        $blogs = Blog::paginate(2);
        if ($slugCategory) {
            $category = BlogCategory::where('slug', $slugCategory)->firstOrFail();
            $blogs = Blog::where('blog_category_id', $category->id)->latest()->paginate(6);
        } else {
            $category = null;
            $blogs = Blog::latest()->paginate(2);
        }
        $newBlog = Blog::orderBy('created_at', 'desc')->first();
        $blogCategories = BlogCategory::all();
        return view('client.pages.blogCategory', compact('blogCategories', 'newBlog', 'blogs'));
    }
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        $relatedBlogs = Blog::where('blog_category_id', $blog->blog_category_id)
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(2)
            ->get();
        return view('client.pages.blogDetail', compact('blog', 'relatedBlogs'));
    }
}
