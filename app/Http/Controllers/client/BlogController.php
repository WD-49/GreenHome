<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request, $slugCategory = null)
    {
        $query = Blog::query();
        if ($slugCategory) {
            $category = BlogCategory::where('slug', $slugCategory)->firstOrFail();
            $query->where('blog_category_id', $category->id);
        } else {
            $category = null;
        }
        if ($request->filled('search')) {
            $keyword = $request->input('search');
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%$keyword%")
                    ->orWhere('summary', 'like', "%$keyword%");
            });
        }
        $blogs = $query->latest()->paginate(6)->appends($request->all());
        $newBlog = Blog::orderBy('created_at', 'desc')->first();
        $blogCategories = BlogCategory::withCount('blogs')->get();

        return view('client.pages.blogCategory', compact('blogCategories', 'newBlog', 'blogs', 'category'));
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
