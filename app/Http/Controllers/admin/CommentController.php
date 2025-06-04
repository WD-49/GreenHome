<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

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

    public function trashed()
    {
        $comments = Comment::onlyTrashed()->with(['user', 'product'])->paginate(10);

        return view('admin.account.comments.trashed', [
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

    public function show($id)
    {
        $comment = Comment::with(['product', 'user'])->findOrFail($id);

        // Lấy các comment khác cùng product_id, trừ chính comment hiện tại
        $relatedComments = Comment::where('product_id', $comment->product_id)
            ->where('id', '!=', $comment->id)
            ->with('user')
            ->latest()
            ->get();

        return view('admin.comments.show', compact('comment', 'relatedComments'));
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

    public function getTrashedComments(Request $request, User $user) // Quan trọng: User $user
    {
        if ($request->ajax()) {
            $trashedComments = $user->comments()->onlyTrashed()->orderBy('deleted_at', 'desc')->get();

            $formattedComments = $trashedComments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'deleted_at' => $comment->deleted_at ? $comment->deleted_at->format('d/m/Y H:i:s') : 'N/A',
                    'restore_url' => route('account.comment.restore', $comment->id), // Kiểm tra tên route này
                    'force_delete_url' => route('account.comment.forceDelete', $comment->id) // Kiểm tra tên route này
                ];
            });
            return response()->json(['comments' => $formattedComments]);
        }
        return abort(403, 'Hành động không được phép.');
    }


    public function getCommentDetailsWithProduct(Request $request, Comment $comment) // Sử dụng Route Model Binding
    {
        if (!$request->ajax()) {
            return abort(403, 'Truy cập không hợp lệ.');
        }

        // Eager load các relationship cần thiết
        // Giả sử Comment có:
        // - user() -> người bình luận
        // - product() -> sản phẩm được bình luận
        $comment->load(['user:id,name', 'product']); // Chỉ lấy id, name của user

        $productData = null;
        if ($comment->product) {
            $imageUrl = $comment->product->image; // Giả sử product có trường 'image' chứa tên file
                                                   // Hoặc $comment->product->images->first()->path nếu là quan hệ nhiều ảnh
            if ($imageUrl && !str_starts_with($imageUrl, 'http')) {
                $imageUrl = Storage::url($imageUrl); // Hoặc asset('storage/' . $imageUrl)
            } elseif (!$imageUrl) {
                $imageUrl = 'https://placehold.co/100x100/EBF0F5/7F8EA3?text=Ảnh+SP'; // Ảnh mặc định
            }

            $productData = [
                'id' => $comment->product->id,
                'name' => $comment->product->name,
                'price' => $comment->product->price, // Giả sử product có trường 'price'
                'slug' => $comment->product->slug,   // Giả sử product có trường 'slug' để tạo link
                'image_url' => $imageUrl,
                // Thêm các trường khác của sản phẩm nếu bạn muốn hiển thị
            ];
        }

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content_full' => $comment->content, // Lấy toàn bộ nội dung
                'created_at_formatted' => $comment->created_at->format('d/m/Y H:i:s'),
                'user_name' => optional($comment->user)->name ?? 'Người dùng ẩn danh',
            ],
            'product' => $productData,
        ]);
    }
}
