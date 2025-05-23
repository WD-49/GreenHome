<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
  public function toggleStatus($id)
{
    $comment = Comment::withTrashed()->findOrFail($id);
    $comment->status = $comment->status == 1 ? 0 : 1;
    $comment->save();

    return back()->with('success', 'Cập nhật trạng thái bình luận thành công.');
}


    public function softDelete($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return back()->with('success', 'Đã ẩn bình luận thành công.');
    }

    public function forceDelete($id)
    {
        $comment = Comment::withTrashed()->findOrFail($id);
        $comment->forceDelete();

        return back()->with('success', 'Đã xóa vĩnh viễn bình luận.');
    }

public function trashed()
{
    $trashedComments = Comment::onlyTrashed()->with('user')->paginate(10);
    $userId = $trashedComments->first()->user_id ?? null;
    return view('admin.account.comments.trashed', compact('trashedComments', 'userId'));
}

public function restore($id)
{
    $comment = Comment::onlyTrashed()->findOrFail($id);
    $comment->restore();

    return redirect()->route('admin.account.comment.trashed')->with('success', 'Khôi phục bình luận thành công.');
}
}
