<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\admin\brand\BrandStoreRequest;
use App\Http\Requests\admin\brand\BrandUpdateRequest;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    // Danh sách thương hiệu + tìm kiếm
    public function index(Request $request)
    {
        $query = Brand::query();

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $brands = $query->orderBy('created_at', 'desc')->paginate(6);

        return view('admin.brands.index', compact('brands'));
    }

    // Form thêm thương hiệu
    public function create()
    {
        return view('admin.brands.create');
    }

    // Lưu thương hiệu mới
   public function store(BrandStoreRequest $request)
{
    $brands = $request->validated()['brands'];
    $insertData = [];

    foreach ($brands as $index => $brand) {
        $slug = Str::slug($brand['name']);

        // Kiểm tra trùng slug
        if (Brand::withTrashed()->where('slug', $slug)->exists()) {
            return back()->withErrors([
                "brands.$index.name" => "❌ Slug của '{$brand['name']}' đã tồn tại.",
            ])->withInput();
        }

        $insertData[] = [
            'name' => $brand['name'],
            'description' => $brand['description'] ?? null,
            'slug' => $slug,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // Lưu tất cả cùng lúc
    Brand::insert($insertData);

    return redirect()->route('admin.brands.index')
                     ->with('success', '✅ Đã thêm ' . count($insertData) . ' thương hiệu thành công!');
}

    // Form chỉnh sửa thương hiệu theo slug
    public function edit($slug)
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();
        return view('admin.brands.edit', compact('brand'));
    }

    // Cập nhật thương hiệu theo slug
    public function update(BrandUpdateRequest $request, $slug)
    {
        $data = $request->validated();

        $brand = Brand::where('slug', $slug)->firstOrFail();
        $brand->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'slug' => Str::slug($data['name']),
        ]);

        return redirect()->route('admin.brands.index')
                         ->with('success', '✏️ Cập nhật thương hiệu thành công!');
    }

    // Xóa mềm thương hiệu theo slug
    public function destroy($slug)
    {
        $brand = Brand::where('slug', $slug)->firstOrFail();
        $brand->delete();

        return redirect()->route('admin.brands.index')->with('success', '🗑️ Đã chuyển thương hiệu vào thùng rác!');
    }

    // Danh sách thương hiệu trong thùng rác
    public function trash()
    {
        $brands = Brand::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);
        return view('admin.brands.trash', compact('brands'));
    }

    // Khôi phục thương hiệu theo slug
    public function restore($slug)
    {
        $brand = Brand::onlyTrashed()->where('slug', $slug)->firstOrFail();
        $brand->restore();

        return redirect()->route('admin.brands.trash')->with('success', '♻️ Khôi phục thương hiệu thành công!');

    }

    // Xóa vĩnh viễn thương hiệu theo slug
    public function forceDelete($slug)
    {
        $brand = Brand::onlyTrashed()->where('slug', $slug)->firstOrFail();
        $brand->forceDelete();

        return redirect()->route('admin.brands.trash')->with('success', '❌ Đã xóa thương hiệu vĩnh viễn!');
    }

    // Hiển thị chi tiết thương hiệu trong admin theo slug (có thể bao gồm thương hiệu đã xóa)
 public function show(Request $request, $slug)
{
    $brand = Brand::withTrashed()->where('slug', $slug)->firstOrFail();

    $query = Product::where('brand_id', $brand->id)->with('category');

    if ($request->filled('keyword')) {
        $query->where('name', 'like', '%' . $request->keyword . '%');
    }
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    if ($request->filled('min_quantity')) {
        $query->where('quantity', '>=', $request->min_quantity);
    }
    $products = $query->latest()->paginate(6)->appends($request->all());
    $categories = Category::all();
    return view('admin.brands.show', compact('brand', 'products', 'categories'));
}


    
}
