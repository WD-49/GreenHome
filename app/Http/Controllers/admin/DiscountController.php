<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscountRequest;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
//     public function store(Request $request )
// {
//     $data = $request->all();

//     $discount = Discount::query()->create($data);
//     session()->flash('test', 'This is a test message');
//     return redirect()->route('admin.discount.index')
//                      ->with('success', 'Mã giảm giá đã được tạo thành công');
// }





/**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    // Quy tắc validate
    $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'code' => 'required|string|max:255|unique:discounts,code',
        'discount_type' => 'required|in:percentage,fixed',
        'discount_value' => 'required|numeric|min:1',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'max_discount' => 'required|numeric|min:0',
        'min_order_value' => 'required|numeric|min:0',
        'quantity' => 'required|integer|min:1',
        'user_usage_limit' => 'required|integer|min:1',
        'applies_to_all_products' => 'required|boolean',
        'status' => 'required|in:active,inactive',
    ];

    // Thông báo lỗi tiếng Việt
    $messages = [
        'title.required' => 'Vui lòng nhập tiêu đề.',
        'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',

        'description.required' => 'Vui lòng nhập mô tả.',
        'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',

        'code.required' => 'Vui lòng nhập mã giảm giá.',
        'code.max' => 'Mã giảm giá không được vượt quá 255 ký tự.',
        'code.unique' => 'Mã giảm giá đã tồn tại.',

        'discount_type.required' => 'Vui lòng chọn loại giảm.',
        'discount_type.in' => 'Loại giảm không hợp lệ.',

        'discount_value.required' => 'Vui lòng nhập giá trị giảm.',
        'discount_value.numeric' => 'Giá trị giảm phải là số.',
        'discount_value.min' => 'Giá trị giảm phải lớn hơn hoặc bằng 1.',

        'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
        'start_date.date' => 'Ngày bắt đầu không hợp lệ.',

        'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
        'end_date.date' => 'Ngày kết thúc không hợp lệ.',
        'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',

        'max_discount.required' => 'Vui lòng nhập giá trị giảm tối đa.',
        'max_discount.numeric' => 'Giá trị giảm tối đa phải là số.',
        'max_discount.min' => 'Giá trị giảm tối đa không được nhỏ hơn 0.',

        'min_order_value.required' => 'Vui lòng nhập giá trị đơn hàng tối thiểu.',
        'min_order_value.numeric' => 'Giá trị đơn hàng tối thiểu phải là số.',
        'min_order_value.min' => 'Giá trị đơn hàng tối thiểu không được nhỏ hơn 0.',

        'quantity.required' => 'Vui lòng nhập số lượng mã.',
        'quantity.integer' => 'Số lượng phải là số nguyên.',
        'quantity.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',

        'user_usage_limit.required' => 'Vui lòng nhập giới hạn sử dụng cho mỗi người dùng.',
        'user_usage_limit.integer' => 'Giới hạn sử dụng phải là số nguyên.',
        'user_usage_limit.min' => 'Giới hạn sử dụng phải lớn hơn hoặc bằng 1.',

        'applies_to_all_products.required' => 'Vui lòng chọn áp dụng cho tất cả sản phẩm hay không.',
        'applies_to_all_products.boolean' => 'Giá trị áp dụng cho tất cả sản phẩm không hợp lệ.',

        'status.required' => 'Vui lòng chọn trạng thái.',
        'status.in' => 'Trạng thái không hợp lệ.',
    ];

    // Tạo Validator
    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        // Trả về lại form với lỗi và dữ liệu cũ
        return redirect()->route('admin.discount.create')
            ->withErrors($validator)
            ->withInput();
    }

    // Lấy dữ liệu hợp lệ
    $data = $validator->validated();

    // Tạo mã giảm giá mới
    Discount::create($data);

    // Flash thông báo thành công
    session()->flash('success', 'Mã giảm giá đã được tạo thành công');

    // Chuyển hướng về trang danh sách
    return redirect()->route('admin.discount.index');
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
