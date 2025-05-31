<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscountRequest;
use App\Models\Discount;
use App\Models\DiscountUsage;
use App\Models\Product;
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
        //lọc theo mã code
        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }
        //lọc theo ngày bắt đầu
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        //lọc theo ngày kết thúc    
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }
        //lọc theo giá trị giảm giá
        if ($request->filled('discount_value')) {
            $query->where('discount_value', '>=', $request->discount_value);
        }
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
        return view('admin.discount.index', [
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
        // //
        // $discounts = Discount::all();
        // return view('admin.discount.create', [
        //     'title' => 'Create Discount',
        // ]);
        $products = Product::whereNull('deleted_at')->get();
        return view('admin.discount.create', compact('products'));
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
            'max_order_value' => 'required|numeric|min:0',
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

            'max_order_value.required' => 'Vui lòng nhập giá trị đơn hàng tối đa.',
            'max_order_value.numeric'  => 'Giá trị đơn hàng tối đa phải là số.',
            'max_order_value.min'      => 'Giá trị đơn hàng tối đa không được nhỏ hơn 0.',

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
        //     $validator = Validator::make($request->all(), $rules, $messages);

        //     if ($validator->fails()) {
        //         // Trả về lại form với lỗi và dữ liệu cũ
        //         return redirect()->route('admin.discount.create')
        //             ->withErrors($validator)
        //             ->withInput();
        //     }

        //     // Lấy dữ liệu hợp lệ
        //     $data = $validator->validated();

        //     // Tạo mã giảm giá mới
        //     Discount::create($data);

        //     // Flash thông báo thành công
        //     session()->flash('success', 'Mã giảm giá đã được tạo thành công');
        //  $discount = Discount::create([
        //             'discount_type' => $request->discount_type,
        //             'discount_value' => $request->discount_value,
        //         ]);

        //         // Gắn sản phẩm
        //         // $discount->products()->sync($request->product_ids ?? []);
        //   $products = Product::whereNull('deleted_at')->get();
        // return view('admin.discount.create', compact('products'));
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            // Lấy lại danh sách sản phẩm để render lại view create
            $products = Product::whereNull('deleted_at')->get();

            return redirect()->route('admin.discount.create')
                ->withErrors($validator)
                ->withInput()
                ->with(compact('products'));
        }
        $validated = $validator->validated();

        // Bước 5: Validate logic: max_order_value >= min_order_value
        if ($validated['max_order_value'] < $validated['min_order_value']) {
            return back()
                ->withErrors(['max_order_value' => 'Giá trị đơn hàng tối đa phải lớn hơn hoặc bằng giá trị tối thiểu.'])
                ->withInput();
        }

        $discount = Discount::create($validator->validated());

        // Nếu không áp dụng cho tất cả sản phẩm, thì lưu các sản phẩm được chọn
        if (!$request->applies_to_all_products && $request->has('product_ids')) {
            $discount->products()->sync($request->product_ids);
        }

        return redirect()->route('admin.discount.index')->with('success', 'Mã giảm giá đã được tạo thành công');
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
    public function history(Request $request)
    {
        $query = DiscountUsage::query();
    {
        //lọc theo mã giảm giá
         if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }
        //lọc theo ngày sử dụng
        if ($request->filled('used_from')) {
            $query->whereDate('used_at', '>=', $request->used_from);
        }
        //lọc theo người dùng
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        //lọc theo sản phẩm
        if ($request->filled('product_id')) {
            $query->whereHas('discount.products', function ($q) use ($request) {
                $q->where('id', $request->product_id);
            });
        }
        //lọc theo mã đơn hàng
        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }
    }
        // // Lấy 20 bản ghi mỗi trang, sắp xếp theo 'used_at' giảm dần
        // // $usages = DiscountUsage::with(['discount', 'user'])->orderByDesc('used_at')->paginate(20);
        // // Lấy 20 bản ghi mỗi trang, sắp xếp theo 'used_at' giảm dần
        // $usages = $query->with(['discount', 'user'])->orderByDesc('used_at')->paginate(20);
        // // $usages = DiscountUsage::with(['discount', 'user'])->orderByDesc('used_at')->paginate(20);
        // // Lấy tất cả các mã giảm giá để hiển thị trong filter
        // $discounts = Discount::all();
        // // Lấy tất cả người dùng để hiển thị trong filter
        // $users = \App\Models\User::all();
        // // Lấy tất cả sản phẩm để hiển thị trong filter
        // $products = Product::whereNull('deleted_at')->get();
        // // Trả về view với các biến cần thiết
        // return view('admin.discount.history', compact('usages', 'discounts', 'users', 'products'));
        
        $usages = DiscountUsage::with(['discount', 'user'])->orderByDesc('used_at')->paginate(20);
$notFound = $usages->isEmpty(); 
    
        return view('admin.discount.history', compact('usages'));
    }
}
