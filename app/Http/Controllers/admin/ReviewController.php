<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $reviews = Review::with(['user', 'productVariant'])->paginate(10);

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
        $review = Review::findOrFail($id);
        $review->status = $request->input('status');
        $review->save();

        return redirect()->route('admin.reviews.index')->with('success', 'Review status updated successfully.');
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



    public function trash()
    {
        // Lấy các review đã bị xóa mềm (soft deleted)
        $reviews = Review::onlyTrashed()->paginate(10);

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
