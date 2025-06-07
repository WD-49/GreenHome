<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\blog\StoreBlogRequest;
use App\Http\Requests\admin\blog\UpdateBlogRequest;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        // Lấy danh sách bài viết (chưa bị xóa), kèm theo thông tin thể loại (category)
        $blogs = Blog::with(['category', 'author'])->latest()->get(); // Thêm author
        // Đếm số bài viết đã bị soft delete
        $deleteCount = Blog::onlyTrashed()->count();

        // Trả về view kèm dữ liệu
        return view('admin.blog.index', compact('blogs', 'deleteCount'));
    }
    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blog.create', compact('categories'));
    }
    public function store(StoreBlogRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?? Str::slug($data['title']);
        if (Blog::where('slug', $data['slug'])->exists()) {
            return back()->withErrors(['slug' => 'Slug đã tồn tại.'])->withInput();
        }
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('images/blogs/thumbnail', 'public');
        }
        if (Auth::user()) {
            $data['author_id'] = Auth::user()->id;
            // dd($data['author_id']);
        }
        Blog::create($data);
        return redirect()->route('admin.blogs.index')->with('success', 'Tạo bài viết thành công!');
    }
    public function show($id)
    {
        $blog = Blog::findOrFail($id);
        $relatedBlogs = Blog::where('blog_category_id', $blog->blog_category_id)
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(5)
            ->get();
        return view('admin.blog.show', compact('blog', 'relatedBlogs'));
    }
    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        $categories = BlogCategory::all();
        return view('admin.blog.edit', compact('blog', 'categories'));
    }
    public function update(UpdateBlogRequest $request, $id)
    {
        $data = $request->validated();
        $blog = Blog::findOrFail($id);
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            // Lưu vào thư mục public/images/blogs/thumbnail
            $path = $file->store('images/blogs/thumbnail', 'public');
            $validatedData['thumbnail'] = $path;
        } else {
            // Giữ lại ảnh cũ nếu không upload mới
            $validatedData['thumbnail'] = $blog->thumbnail ?? null;
        }
        $blog->update($data);
        return redirect()->route('admin.blogs.index')->with('success', 'Cập nhật bài viết thành công!');
    }
    public function destroy(Request $request)
    {
        $id = $request->input('id');
        $blog = Blog::findOrFail($id);
        $blog->delete();
        return redirect()->route('admin.blogs.index')->with('success', 'Xóa bài viết thành công!');
    }
}
