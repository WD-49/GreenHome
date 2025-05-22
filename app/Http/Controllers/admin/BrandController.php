<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Requests\admin\attribute\BrandRequest;

class BrandController extends Controller
{
    // Danh sách thương hiệu + tìm kiếm
    public function index(Request $request)
    {
        $query = Brand::query();

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $brands = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.brands.index', compact('brands'));
    }

    // Form thêm thương hiệu
    public function create()
    {
        return view('admin.brands.create');
    }

    // Xử lý thêm mới thương hiệu
    public function store(BrandRequest $request)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:brands,name',
            'description' => 'nullable|string',
        ]);

        Brand::create($request->only(['name', 'description']));

        return redirect()->route('admin.brands.index')->with('success', '✅ Thêm thương hiệu thành công!');
    }

    // Form chỉnh sửa thương hiệu
    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    // Cập nhật thương hiệu
    public function update(BrandRequest $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:brands,name,' . $id,
            'description' => 'required|string',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->update($request->only(['name', 'description']));

        return redirect()->route('admin.brands.index')->with('success', '✏️ Cập nhật thương hiệu thành công!');
    }

    // Xóa mềm thương hiệu
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', '🗑️ Đã chuyển thương hiệu vào thùng rác!');
    }

    // Danh sách thương hiệu trong thùng rác
    public function trash()
    {
        $brands = Brand::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);
        return view('admin.brands.trash', compact('brands'));
    }

    // Khôi phục thương hiệu
    public function restore($id)
    {
        $brand = Brand::onlyTrashed()->findOrFail($id);
        $brand->restore();

        return redirect()->route('admin.brands.trashed')->with('success', '♻️ Khôi phục thương hiệu thành công!');
    }

    // Xóa vĩnh viễn thương hiệu
    public function forceDelete($id)
    {
        $brand = Brand::onlyTrashed()->findOrFail($id);
        $brand->forceDelete();

        return redirect()->route('admin.brands.trash')->with('success', '❌ Đã xóa thương hiệu vĩnh viễn!');
    }

    // Hiển thị chi tiết thương hiệu
    public function show($id)
    {
        $brand = Brand::withTrashed()->findOrFail($id);
        return view('admin.brands.show', compact('brand'));
    }
}
