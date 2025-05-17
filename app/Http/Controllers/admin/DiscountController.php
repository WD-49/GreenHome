<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscountRequest;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
//    public function index(Request $request)
// {
//     // Lấy 20 bản ghi mỗi trang, sắp xếp theo 'id' giảm dần (có thể thay bằng trường khác nếu cần)
//     // $discounts = Discount::orderBy('id', 'desc')->paginate(20);
//         $query = Discount::query();

//     if ($request->filled('keyword')) {
//         $keyword = $request->keyword;
//         $query->where(function ($q) use ($keyword) {
//             $q->where('title', 'like', '%' . $keyword . '%')
//               ->orWhere('code', 'like', '%' . $keyword . '%');
//         });
//     }

//     $discounts = $query->orderBy('id', 'desc')->paginate(20);
// $notFound = $discounts->isEmpty(); 
//     return view('admin.discount', [


        
//         'title' => 'Discounts',
//         'discounts' => $discounts,
//         'notFound' => $notFound,
//     ]);
// }
public function index(Request $request)
{
    $query = Discount::query();

    // Lọc theo loại giảm giá
    if ($request->filled('type')) {
        $query->where('discount_type', $request->type);
    }

    // Lọc theo trạng thái: active hoặc inactive
    if ($request->filled('status')) {
        $query->where('status', $request->status); // giả sử cột status có giá trị 'active' hoặc 'inactive'
    }

    // Lọc theo ngày tạo
    if ($request->filled('created_from')) {
        $query->whereDate('created_at', '>=', $request->created_from);
    }

    if ($request->filled('created_to')) {
        $query->whereDate('created_at', '<=', $request->created_to);
    }

    $discounts = $query->orderBy('id', 'desc')->paginate(20);
 $notFound = $discounts->isEmpty(); 
    return view('admin.discount', [
        'title' => 'Discounts',
        'discounts' => $discounts,
        'notFound' => $notFound,
    ]);
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $discounts = Discount::all();
        return view('admin.discount.create', [
            'title' => 'Create Discount',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDiscountRequest $request)
{
    $data = $request->all();

    $discount = Discount::query()->create($data);
    session()->flash('test', 'This is a test message');
    return redirect()->route('admin.discount.index')
                     ->with('success', 'Mã giảm giá đã được tạo thành công');
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
       

           $discount = Discount::findOrFail($id); // Tự động trả về 404 nếu không tìm thấy
//  dd($discount);
    return view('admin.discount.show', compact('discount'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $discount = Discount::find($id);
       
        return view('admin.discount.edit', [
            'title' => 'Edit Discount',
            'discount' => $discount,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $data = $request->all();
        $discount = Discount::find($id);
        $discount->update($data);
        return redirect()->route('admin.discount.index')->with('success', 'Mã giảm giá đã được cập nhật thành công');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $discount = Discount::find($id);
        $discount->delete();        
        return redirect()->route('admin.discount.index')->with('success', 'Mã giảm giá đã được xóa thành công');
    }
    public function trash()
{
    $discounts = Discount::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(20);
    return view('admin.discount.trash', compact('discounts'));
}

public function restore($id)
{
    $discount = Discount::withTrashed()->findOrFail($id);
    $discount->restore();
    return redirect()->route('admin.discount.trash')->with('success', 'Khôi phục thành công!');
}

public function forceDelete($id)
{
    $discount = Discount::withTrashed()->findOrFail($id);
    $discount->forceDelete();
    return redirect()->route('admin.discount.trash')->with('success', 'Đã xóa vĩnh viễn!');
}

}
