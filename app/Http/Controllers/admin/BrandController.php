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
        return view('admin.brands.index', compact('brands'));
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

        return redirect()->route('brands.index')->with('success', 'Thêm thương hiệu thành công!');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $brand->update($request->all());

        return redirect()->route('brands.index')->with('success', 'Cập nhật thương hiệu thành công!');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('brands.index')->with('success', 'Đã chuyển vào thùng rác!');
    }

    public function trash()
    {
        $brands = Brand::onlyTrashed()->paginate(10);
        return view('admin.brands.trash', compact('brands'));
    }

    public function restore($id)
    {
        Brand::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('brands.trash')->with('success', 'Khôi phục thành công!');
    }

    public function forceDelete($id)
    {
        Brand::onlyTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('brands.trash')->with('success', 'Đã xóa vĩnh viễn!');
    }
}
