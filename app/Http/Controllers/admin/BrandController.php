<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Requests\admin\attribute\BrandRequest;

class BrandController extends Controller
{
    // Hiển thị danh sách thương hiệu + tìm kiếm
    public function index(Request $request)
    {
        $query = Brand::query();

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $brands = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.brands.index', compact('brands'));
    }

    // Hiển thị form thêm mới thương hiệu
    public function create()
    {
        return view('admin.brands.create');
    }

    // Xử lý lưu thương hiệu mới
    public function store(BrandRequest $request)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:brands,name',
            'description' => 'required|string',
        ]);

        Brand::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Thêm thương hiệu thành công!');
    }

    // Hiển thị form chỉnh sửa thương hiệu
    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    // Xử lý cập nhật thương hiệu
    public function update(BrandRequest $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:brands,name,' . $id,
            'description' => 'required|string',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Cập nhật thương hiệu thành công!');
    }

    // Xóa mềm (chuyển vào thùng rác)
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', 'Đã chuyển vào thùng rác!');
    }

    // Hiển thị danh sách thương hiệu đã bị xóa (thùng rác)
    public function trash()
    {
        $brands = Brand::onlyTrashed()->paginate(10);
        return view('admin.brands.trash', compact('brands'));
    }

    // Khôi phục thương hiệu từ thùng rác
    public function restore($id)
    {
        Brand::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.brands.trash')->with('success', 'Khôi phục thành công!');
    }

    // Xóa vĩnh viễn thương hiệu
    public function forceDelete($id)
    {
        Brand::onlyTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('admin.brands.trash')->with('success', 'Đã xóa vĩnh viễn!');
    }

    // Hiển thị chi tiết thương hiệu (có thể cả bản đã xóa)
    public function show($id)
    {
        $brand = Brand::withTrashed()->findOrFail($id);
        return view('admin.brands.show', compact('brand'));
    }
}
