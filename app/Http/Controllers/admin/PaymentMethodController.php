<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
   public function index(Request $request)
{
    $query = PaymentMethod::query();

    // Lọc theo tên nếu có
    if ($request->has('name') && $request->name !== null) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    $paymentMethods = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('admin.payment_methods.index', compact('paymentMethods'));
}


    public function create()
    {
        return view('admin.payment_methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:payment_methods,name',
            'description' => 'nullable|max:1000',
            'status' => 'required|boolean',
        ], [
            'name.required' => 'Vui lòng nhập tên phương thức thanh toán.',
            'name.unique' => 'Tên phương thức đã tồn tại.',
            'name.max' => 'Tên không quá 255 ký tự.',
            'description.max' => 'Mô tả không quá 1000 ký tự.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.boolean' => 'Trạng thái không hợp lệ.',
        ]);
        PaymentMethod::create($validated);
        return redirect()->route('admin.paymentMethods.index')->with('success', 'Thêm phương thức thành công!');
    }

    public function edit($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        return view('admin.payment_methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, $id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|max:255|unique:payment_methods,name,' . $id,
            'description' => 'nullable|max:1000',
            'status' => 'required|boolean',
        ], [
            'name.required' => 'Vui lòng nhập tên phương thức thanh toán.',
            'name.unique' => 'Tên phương thức đã tồn tại.',
            'name.max' => 'Tên không quá 255 ký tự.',
            'description.max' => 'Mô tả không quá 1000 ký tự.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.boolean' => 'Trạng thái không hợp lệ.',
        ]);
        $paymentMethod->update($validated);
        return redirect()->route('admin.paymentMethods.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy($id)
    {
        $paymentMethod = PaymentMethod::findOrFail($id);
        $paymentMethod->delete();
        return redirect()->route('admin.paymentMethods.index')->with('success', 'Đã chuyển vào thùng rác!');
    }

  public function trash(Request $request)
{
    $query = PaymentMethod::onlyTrashed();

    // Lọc theo tên
    if ($request->has('name') && $request->name !== null) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    $paymentMethods = $query->orderBy('deleted_at', 'desc')->paginate(10);

    return view('admin.payment_methods.trash', compact('paymentMethods'));
}


    public function restore($id)
    {
        $paymentMethod = PaymentMethod::onlyTrashed()->findOrFail($id);
        $paymentMethod->restore();
        return redirect()->route('admin.paymentMethods.trash')->with('success', 'Khôi phục thành công!');
    }

    public function forceDelete($id)
    {
        $paymentMethod = PaymentMethod::onlyTrashed()->findOrFail($id);
        $paymentMethod->forceDelete();
        return redirect()->route('admin.paymentMethods.trash')->with('success', 'Xóa vĩnh viễn thành công!');
    }
}
