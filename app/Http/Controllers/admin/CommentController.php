<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        if ($request->filled('from_date')) {
            $comments->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $comments->whereDate('created_at', '<=', $request->to_date);
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


    public function restore($id)
    {
        $comment = Comment::onlyTrashed()->findOrFail($id);
        $comment->restore();

        return redirect()->back()->with('success', 'Đã khôi phục bình luận.');
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

    public function trash()
    {
        $comments = Comment::onlyTrashed()->with(['user', 'product'])->paginate(10);

        return view('admin.comments.trash', [
            'title' => 'Thùng rác bình luận',
            'comments' => $comments,
        ]);
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
        Log::info('---- DEBUG COMMENT DETAILS ----');
        Log::info('Comment Data:', $comment->toArray()); // Ghi comment thành mảng

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

    // Hàm helper để tạo response cho việc cập nhật status
    private function getStatusResponseData(Comment $comment)
    {
        $statusText = '';
        $statusClassBadge = '';
        $actionsHtml = ''; // HTML cho các nút hành động mới

        // Nút xem chi tiết luôn có
        $actionsHtml .= '<button class="btn btn-sm btn-outline-info view-comment-details-btn me-1" data-comment-id="' . $comment->id . '" title="Xem chi tiết"><i class="fas fa-eye"></i></button>';

        switch ($comment->status) {
            case 'hiển thị':
                $statusText = 'Đang hiển thị';
                $statusClassBadge = 'bg-success';
                $actionsHtml .= '<button class="btn btn-sm btn-outline-secondary hide-comment-btn me-1" data-comment-id="' . $comment->id . '" title="Ẩn bình luận"><i class="fas fa-eye-slash"></i></button>';
                break;
            case 'ẩn':
                $statusText = 'Bị ẩn';
                $statusClassBadge = 'bg-warning text-dark';
                $actionsHtml .= '<button class="btn btn-sm btn-outline-info show-again-comment-btn me-1" data-comment-id="' . $comment->id . '" title="Hiện lại bình luận"><i class="fas fa-redo-alt"></i></button>';
                break;
            case 'chưa duyệt':
            default:
                $statusText = 'Chưa duyệt';
                $statusClassBadge = 'bg-secondary';
                $actionsHtml .= '<button class="btn btn-sm btn-outline-primary approve-comment-btn me-1" data-comment-id="' . $comment->id . '" title="Duyệt bình luận"><i class="fas fa-check"></i></button>';
                break;
        }
        // Nút xóa mềm luôn có cho bình luận active
        if (!$comment->trashed()) {
            $actionsHtml .= '<button class="btn btn-sm btn-outline-danger soft-delete-comment-btn" data-comment-id="' . $comment->id . '" title="Xóa mềm"><i class="fas fa-trash-alt"></i></button>';
        }

        return [
            'comment_id' => $comment->id,
            'new_status_text' => $statusText,
            'new_status_class_badge' => $statusClassBadge,
            'new_actions_html' => $actionsHtml,
        ];
    }

    public function approveCommentAjax(Request $request, Comment $comment)
    {
        $comment->status = 'hiển thị';
        $comment->save();

        if ($request->ajax()) {
            return response()->json(array_merge(
                ['success' => true, 'message' => 'Đã duyệt bình luận.'],
                $this->getStatusResponseData($comment)
            ));
        }
        return redirect()->back()->with('success', 'Đã duyệt bình luận.');
    }

    public function hideCommentAjax(Request $request, Comment $comment)
    {
        $comment->status = 'ẩn';
        $comment->save();

        if ($request->ajax()) {
            return response()->json(array_merge(
                ['success' => true, 'message' => 'Đã ẩn bình luận.'],
                $this->getStatusResponseData($comment)
            ));
        }
        return redirect()->back()->with('success', 'Đã ẩn bình luận.');
    }


    public function showAgainCommentAjax(Request $request, Comment $comment)
    {
        if ($comment->status === 'ẩn') {
            $comment->status = 'hiển thị'; // Hoặc 'chưa duyệt' nếu bạn muốn quy trình lại từ đầu
            $comment->save();
            if ($request->ajax()) {
                return response()->json(array_merge(
                    ['success' => true, 'message' => 'Đã hiện lại bình luận.'],
                    $this->getStatusResponseData($comment)
                ));
            }
            return redirect()->back()->with('success', 'Đã hiện lại bình luận.');
        }
        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Bình luận không ở trạng thái "ẩn".'], 422);
        }
        return redirect()->back()->with('error', 'Bình luận không ở trạng thái "ẩn".');
    }

    // Sửa lại phương thức destroy của bạn thành softDeleteCommentAjax
    public function softDeleteCommentAjax(Request $request, Comment $comment)
    {
        $comment->delete(); // Đây là soft delete nếu Comment model dùng SoftDeletes trait

        if ($request->ajax()) {
            // Lấy số lượng comment mới của user này (bao gồm cả trong thùng rác)
            $totalCommentsForUser = Comment::where('user_id', $comment->user_id)->withTrashed()->count();
            return response()->json([
                'success' => true,
                'message' => 'Đã chuyển bình luận vào thùng rác!',
                'comment_id' => $comment->id, // Trả về ID để JS xóa hàng
                'new_total_comment_count' => $totalCommentsForUser, // Để cập nhật badge trên tab Bình luận
            ]);
        }
        return redirect()->back()->with('success', 'Đã xóa (tạm thời) bình luận.');
    }

    public function restoreCommentAjax(Request $request, $id) // Route của bạn đang dùng {id}
    {
        $comment = Comment::onlyTrashed()->findOrFail($id);
        $comment->restore();
        // Sau khi khôi phục, trạng thái có thể là 'chưa duyệt' hoặc trạng thái cũ trước khi xóa
        // $comment->status = 'chưa duyệt'; // Ví dụ
        // $comment->save();


        if ($request->ajax()) {
            // Lấy số lượng comment mới của user này (bao gồm cả trong thùng rác)
            $totalCommentsForUser = Comment::where('user_id', $comment->user_id)->withTrashed()->count();
            return response()->json([
                'success' => true,
                'message' => 'Đã khôi phục bình luận.',
                'comment_id' => $comment->id, // Trả về ID để JS xóa hàng khỏi thùng rác
                'new_total_comment_count' => $totalCommentsForUser, // Để cập nhật badge trên tab Bình luận
                // Bạn có thể trả thêm thông tin comment đã khôi phục nếu muốn thêm lại vào danh sách active
                'restored_comment_html' => $this->generateActiveCommentRowHtml($comment) // Hàm này bạn cần tự tạo
            ]);
        }
        return redirect()->back()->with('success', 'Đã khôi phục bình luận.');
    }

    // Hàm ví dụ để tạo HTML cho một hàng bình luận (bạn cần tùy chỉnh cho giống Blade)
    private function generateActiveCommentRowHtml(Comment $comment)
    {
        // Logic tạo HTML tương tự như trong Blade của bạn
        // Ví dụ sơ lược:
        $statusData = $this->getStatusResponseData($comment); // Lấy data cho status và actions
        return view('admin.partials._comment_row', ['comment' => $comment, 'statusData' => $statusData])->render();
    }

    // Các phương thức khác như forceDelete, trashed (cho trang riêng), getTrashedComments (AJAX) ...
    // ...
    public function getTrashedComments(Request $request, User $user)
    {
        if ($request->ajax()) {
            $trashedComments = $user->comments()
                ->with('user:id,name')
                ->onlyTrashed()
                ->orderBy('deleted_at', 'desc')
                ->get();

            $formattedComments = $trashedComments->map(function ($comment) {
                // SỬA TÊN ROUTE Ở ĐÂY CHO ĐÚNG
                // Dựa trên định nghĩa route của bạn: Route::post('/restore/{comment}', ...)->name('restoreComment');
                // và giả định có prefix 'admin.account.comment.'
                $restoreRouteName = 'admin.account.comment.restoreComment';

                return [
                    'id' => $comment->id,
                    'content' => Str::limit($comment->content, 150),
                    'user_name' => optional($comment->user)->name ?? 'Người dùng không xác định',
                    'deleted_at' => $comment->deleted_at ? $comment->deleted_at->format('d/m/Y H:i:s') : 'N/A',
                    // Route của bạn cho restore là '/restore/{comment}', nên truyền 'comment'
                    'restore_url' => route($restoreRouteName, ['comment' => $comment->id]),
                ];
            });
            return response()->json(['comments' => $formattedComments]);
        }
        return abort(403, 'Hành động không được phép.');
    }


    // HÀM HELPER TẠO DỮ LIỆU CHO UI (TRẠNG THÁI VÀ NÚT BẤM)
    private function getCommentUIData(Comment $comment)
    {
        $statusText = '';
        $statusClassBadge = '';
        $actionsHtml = '';

        // Nút Xem Chi Tiết (luôn có cho bình luận active)
        if (!$comment->trashed()) {
            $actionsHtml .= '<button class="btn btn-xs btn-outline-info view-comment-details-btn me-1" data-comment-id="' . $comment->id . '" title="Xem chi tiết" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-eye"></i></button>';
        }

        // Nút hành động dựa trên trạng thái hiện tại
        switch ($comment->status) {
            case 'hiển thị':
                $statusText = 'Đang hiển thị';
                $statusClassBadge = 'bg-success';
                $actionsHtml .= '<button class="btn btn-xs btn-outline-secondary change-comment-status-btn me-1" data-comment-id="' . $comment->id . '" data-action="hide" title="Ẩn bình luận này" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-eye-slash"></i></button>';
                break;
            case 'ẩn':
                $statusText = 'Bị ẩn';
                $statusClassBadge = 'bg-warning text-dark';
                $actionsHtml .= '<button class="btn btn-xs btn-outline-info change-comment-status-btn me-1" data-comment-id="' . $comment->id . '" data-action="show_again" title="Hiện lại bình luận này" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-redo-alt"></i></button>';
                break;
            case 'chưa duyệt':
            default: // Mặc định hoặc nếu status không khớp, coi là 'chưa duyệt'
                $statusText = 'Chưa duyệt';
                $statusClassBadge = 'bg-secondary';
                $actionsHtml .= '<button class="btn btn-xs btn-outline-primary change-comment-status-btn me-1" data-comment-id="' . $comment->id . '" data-action="approve" title="Duyệt bình luận này" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-check"></i></button>';
                // Có thể thêm nút "Ẩn" trực tiếp từ trạng thái "chưa duyệt" nếu muốn
                $actionsHtml .= '<button class="btn btn-xs btn-outline-secondary change-comment-status-btn ms-1 me-1" data-comment-id="' . $comment->id . '" data-action="hide" title="Ẩn bình luận (từ chưa duyệt)" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-eye-slash"></i></button>';
                break;
        }

        // Nút Xóa Mềm (luôn có cho bình luận active)
        if (!$comment->trashed()) {
            $actionsHtml .= '<button class="btn btn-xs btn-outline-danger soft-delete-comment-btn" data-comment-id="' . $comment->id . '" title="Chuyển vào thùng rác" data-bs-toggle="tooltip" data-bs-placement="top"><i class="fas fa-trash-alt"></i></button>';
        }

        return [
            'comment_id' => $comment->id, // Thêm ID để JS có thể dùng nếu cần
            'status_text' => $statusText,
            'status_class_badge' => $statusClassBadge,
            'actions_html' => $actionsHtml,
            // Dữ liệu cần thiết khác để JS có thể render lại hàng nếu không dùng partial view
            'content_short' => Str::limit($comment->content, 70),
            'created_at_formatted' => $comment->created_at->format('d/m/Y H:i'),
        ];
    }
}
