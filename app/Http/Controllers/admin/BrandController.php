<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::latest()->paginate(10);
        return view('admin.brands.index', compact('brands')); // ✅ Đúng đường dẫn view
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Brand::create($request->all());

        return redirect()->route('admin.brands.index')->with('success', 'Thêm thương hiệu thành công!');
    }

    public function edit($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $brand = Brand::findOrFail($id);
        $brand->update($request->all());

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
