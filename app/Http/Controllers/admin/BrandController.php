<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Requests\admin\attribute\BrandRequest;

class BrandController extends Controller
{
    public function index(Request $request)
{
    // Tạo query ban đầu
    $query = Brand::query();

    // Thêm điều kiện tìm kiếm nếu có
    if ($request->filled('keyword')) {
        $query->where('name', 'like', '%' . $request->keyword . '%');
    }

    // Sắp xếp mới nhất và phân trang
    $brands = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('admin.brands.index', compact('brands'));
}

    public function store( BrandRequest $request)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:brands,name',
            'description' => 'required|string',
            
        ]);

        Brand::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Thêm thương hiệu thành công!');
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update( BrandRequest $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:20|unique:brands,name,' . $id,
            'description' => 'required|string',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->update([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return redirect()->route('admin.brands.index')->with('success', 'Cập nhật thương hiệu thành công!');
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Đã chuyển vào thùng rác!');
    }

    public function trash()
    {
        $brands = Brand::onlyTrashed()->paginate(10);
        return view('admin.brands.trash', compact('brands'));
    }

    public function restore($id)
    {
        Brand::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.brands.trashed')->with('success', 'Khôi phục thành công!');
    }

    public function forceDelete($id)
    {
        Brand::onlyTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('admin.brands.trashed')->with('success', 'Đã xóa vĩnh viễn!');
    }

    public function show($id)
    {
        $brand = Brand::withTrashed()->findOrFail($id);
        return view('admin.brands.show', compact('brand'));
    }
}
