<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Product;
use App\Models\User;

class CommentController extends Controller
{
    public function index(Request $request)
{
    $comments = Comment::with(['product', 'user'])->latest();

    // Lọc theo tên sản phẩm (input product_name)
    if ($request->filled('product_name')) {
        $comments->whereHas('product', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->product_name . '%');
        });
    }

    // Lọc theo ngày
    if ($request->filled('min_date')) {
        $comments->whereDate('created_at', '>=', $request->min_date);
    }
    if ($request->filled('max_date')) {
        $comments->whereDate('created_at', '<=', $request->max_date);
    }

    // Lọc theo trạng thái
    if ($request->filled('status')) {
        $comments->where('status', $request->status);
    }

    // Lọc theo tên user
    if ($request->filled('user_name')) {
        $comments->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->user_name . '%');
        });
    }

    $comments = $comments->paginate(10);

    // Nếu bạn muốn dùng danh sách sản phẩm (ví dụ cho dropdown hoặc autocomplete), giữ lấy tất cả
    $products = Product::all();

    return view('admin.comments.index', [
        'title' => 'Quản lý bình luận',
        'comments' => $comments,
        'products' => $products,
        'request' => $request,
    ]);
}

    public function approve(Request $request)
    {
        $comment = Comment::findOrFail($request->id);
        $comment->update(['status' => 'hiển thị']);

        return redirect()->back()->with('success', 'Đã duyệt bình luận.');
    }

    public function hide(Request $request)
    {
        $comment = Comment::findOrFail($request->id);
        $comment->update(['status' => 'ẩn']);

        return redirect()->back()->with('success', 'Đã ẩn bình luận.');
    }

    public function destroy(Request $request)
    {
        $comment = Comment::findOrFail($request->id);
        $comment->delete();

        return redirect()->back()->with('success', 'Đã xóa (tạm thời) bình luận.');
    }

    public function trash()
    {
        $comments = Comment::onlyTrashed()->with(['user', 'product'])->paginate(10);

        return view('admin.comments.trash', [
            'title' => 'Thùng rác bình luận',
            'comments' => $comments,
        ]);
    }

    public function restore($id)
    {
        $comment = Comment::onlyTrashed()->findOrFail($id);
        $comment->restore();

        return redirect()->back()->with('success', 'Đã khôi phục bình luận.');
    }

    public function forceDelete(Request $request)
    {
        $comment = Comment::onlyTrashed()->findOrFail($request->id);
        $comment->forceDelete();

        return redirect()->back()->with('success', 'Đã xóa vĩnh viễn bình luận.');
    }



public function show(Request $request, $id)
{
    $comment = Comment::with(['product.category', 'user.profile'])->findOrFail($id);

    // Query khởi tạo với comment cùng sản phẩm
    $relatedComments = Comment::where('product_id', $comment->product_id)
                              ->where('id', '!=', $comment->id)
                              ->with(['user.profile', 'product.category', 'product.brand']);

    // Lọc theo tên user
    if ($request->filled('user_name')) {
        $relatedComments->whereHas('user', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->user_name . '%');
        });
    }

    // Lọc theo trạng thái
    if ($request->filled('status')) {
        $relatedComments->where('status', $request->status);
    }

    // Lọc theo ngày
    if ($request->filled('min_date')) {
        $relatedComments->whereDate('created_at', '>=', $request->min_date);
    }
    if ($request->filled('max_date')) {
        $relatedComments->whereDate('created_at', '<=', $request->max_date);
    }

    // Lọc theo thương hiệu
    if ($request->filled('brand_id')) {
        $relatedComments->whereHas('product.brand', function ($q) use ($request) {
            $q->where('id', $request->brand_id);
        });
    }

    // Lọc theo danh mục
    if ($request->filled('category_id')) {
        $relatedComments->whereHas('product.category', function ($q) use ($request) {
            $q->where('id', $request->category_id);
        });
    }

    $relatedComments = $relatedComments->latest()->paginate(5)->appends($request->query());

    // Lấy danh sách danh mục, thương hiệu để hiển thị form lọc
    $brands = \App\Models\Brand::all();
    $categories = \App\Models\Category::all();

    return view('admin.comments.show', compact(
        'comment',
        'relatedComments',
        'brands',
        'categories',
        'request'
    ));
}





    public function showAgain(Request $request)
{
    $comment = Comment::findOrFail($request->id);
    if ($comment->status === 'ẩn') {
        $comment->update(['status' => 'hiển thị']);
        return redirect()->back()->with('success', 'Đã hiện lại bình luận.');
    }
    return redirect()->back()->with('error', 'Bình luận không ở trạng thái ẩn.');
}

}
