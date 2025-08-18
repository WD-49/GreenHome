<?php

namespace App\Http\Controllers\Admin;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Http\Controllers\Controller;
use App\Notifications\ReviewStatusNotification;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //


        $reviews = Review::query();

        if ($request->filled('rating')) {
            $reviews->where('rating', $request->rating);
        }

        if ($request->filled('status')) {
            $reviews->where('status', $request->status);
        }

        $reviews = $reviews->with(['user', 'productVariant.product'])->orderby('id', 'desc')->paginate(10);


        // Test: dd($reviews->pluck('rating'));

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productVariants = ProductVariant::all();

        return view('admin.reviews.create', [
            'productVariants' => $productVariants,

        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    public function updateStatus(Request $request, string $id)
    {
        $review = Review::with('productVariant.product')->findOrFail($id);
        $review->status = $request->input('status');
        $review->save();
        $review->user->notify(new ReviewStatusNotification($review, $request->input('status')));

        return redirect()->route('admin.reviews.index')->with('success', 'Cập nhật trạng thái đánh giá thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $review = Review::with(['user', 'productVariant'])->findOrFail($id);
        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $review = Review::findOrFail($id);
        $review->delete();
        return redirect()->route('admin.reviews.index')->with('success', 'Review deleted successfully.');
    }



    public function trash(Request $request)
    {
        $query = Review::onlyTrashed();

        // Lọc theo số sao nếu có
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Lọc theo trạng thái nếu có
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lấy dữ liệu đã phân trang và kèm theo quan hệ
        $reviews = $query->with(['productVariant', 'user'])->paginate(10);

        // Giữ nguyên query string khi phân trang
        $reviews->appends($request->only(['rating', 'status']));

        return view('admin.reviews.trash', compact('reviews'));
    }

    public function restore($id)
    {
        $review = Review::withTrashed()->findOrFail($id);
        $review->restore();

        return redirect()->route('admin.reviews.trash')->with('success', 'Đánh giá đã được phục hồi thành công.');
    }

    public function forceDelete($id)
    {
        $review = Review::withTrashed()->findOrFail($id);
        $review->forceDelete();

        return redirect()->route('admin.reviews.trash')->with('success', 'Đánh giá đã bị xóa vĩnh viễn.');
    }
}
