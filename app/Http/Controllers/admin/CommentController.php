<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Trang danh sách bình luận, hỗ trợ filter
    public function index(Request $request)
    {
        $comments = Comment::with(['product', 'user'])
            ->when($request->product_name, function ($query) use ($request) {
                $query->whereHas('product', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->product_name . '%');
                });
            })
            ->when($request->user_name, function ($query) use ($request) {
                $query->whereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->user_name . '%');
                });
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->comment_date, function ($query) use ($request) {
                $query->whereDate('created_at', $request->comment_date);
            })
            ->latest()
            ->paginate(10);

        $title = 'Quản lý bình luận';

        return view('admin.comments.index', compact('comments', 'title'));
    }

    // Xóa mềm (soft delete)
    public function destroy(Request $request)
    {
        $comment = Comment::findOrFail($request->id);
        $comment->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa bình luận (soft delete).']);
    }

    // Trang thùng rác (danh sách bình luận đã xóa mềm)
    public function trash(Request $request)
    {
        $comments = Comment::onlyTrashed()
            ->with(['product', 'user'])
            ->latest()
            ->paginate(10);

        $title = 'Thùng rác bình luận';

        return view('admin.comments.trash', compact('comments', 'title'));
    }

    // Phục hồi bình luận từ thùng rác
    public function restore(Request $request)
    {
        $comment = Comment::onlyTrashed()->findOrFail($request->id);
        $comment->restore();
        return redirect()->route('admin.comments.trash')->with('success', 'Đã phục hồi bình luận.');

    }

    // Xóa vĩnh viễn bình luận (force delete)
    public function forceDelete(Request $request)
    {
        $comment = Comment::onlyTrashed()->findOrFail($request->id);
        $comment->forceDelete();
        return redirect()->route('admin.comments.trash')->with('success', 'Đã xóa vĩnh viễn bình luận.');
    }

    // Chấp thuận bình luận (approve)
    public function approve(Request $request)
    {
        $comment = Comment::findOrFail($request->id);
        $comment->update(['status' => 'hiển thị']);
        return response()->json(['success' => true]);
    }

    // Ẩn bình luận
    public function hide(Request $request)
    {
        $comment = Comment::findOrFail($request->id);
        $comment->update(['status' => 'ẩn']);
        return response()->json(['success' => true]);
    }
}
