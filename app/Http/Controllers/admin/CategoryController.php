<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        // Đếm cho tabs
        $categoryAll = Category::withTrashed()->get();
        $categoryActive = Category::whereNull('deleted_at')->get();
        $categoryTrashed = Category::onlyTrashed()->get();

        $query = Category::query();

        // Filter theo search (tên hoặc slug)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('slug', 'like', '%' . $search . '%');
            });
        }

        // Filter trạng thái
        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->whereNull('deleted_at');
            }
            if ($request->status == 'deleted') {
                $query->onlyTrashed();
            }
        }

        // Filter ngày tạo
        if ($request->filled('min_date')) {
            $query->whereDate('created_at', '>=', $request->min_date);
        }
        if ($request->filled('max_date')) {
            $query->whereDate('created_at', '<=', $request->max_date);
        }

        $categories = $query->orderBy('created_at', 'DESC')->paginate(10);

        return view('admin.categories.index', [
            'categories' => $categories,
            'categoryAll' => $categoryAll,
            'categoryActive' => $categoryActive,
            'categoryTrashed' => $categoryTrashed,
            'title' => 'Danh sách danh mục',
        ]);
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'array'], // Mảng tên danh mục
            'name.*' => ['required', 'string', 'max:255', 'distinct'], // Kiểm tra tên duy nhất
            'description' => ['nullable', 'array'], // Mảng mô tả
            'description.*' => ['nullable', 'string'],
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.*.required' => 'Tên danh mục không được để trống.',
            'name.*.distinct' => 'Tên danh mục không được trùng.',
        ]);

        // Kiểm tra xem mảng 'name' và 'description' có tồn tại và có phải là mảng không
        if ($request->has('name') && is_array($request->name)) {
            foreach ($request->name as $index => $name) {
                $slug = Str::slug($name);

                // Kiểm tra slug có bị trùng hay không
                $existingSlug = Category::where('slug', $slug)->first();
                if ($existingSlug) {
                    $slug = $slug . '-' . uniqid();
                }

                // Tạo mới danh mục
                $category = new Category();
                $category->name = $name;
                $category->slug = $slug;
                $category->description = isset($request->description[$index]) ? $request->description[$index] : null;
                $category->save();
            }

            return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công.');
        } else {
            // Trường hợp không có dữ liệu 'name' hợp lệ
            return back()->withErrors(['name' => 'Danh mục không hợp lệ.']);
        }
    }


    public function edit($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
        ]);

        // Tạo slug duy nhất (ngoại trừ bản ghi hiện tại)
        $slugNew = Str::slug($request->name);
        $existingSlug = Category::where('slug', $slugNew)->where('id', '!=', $category->id)->first();
        if ($existingSlug) {
            $slugNew = $slugNew . '-' . uniqid();
        }

        $category->name = $request->name;
        $category->description = $request->description;
        $category->slug = $slugNew;
        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Đã chuyển danh mục vào thùng rác.');
    }

    public function restore($slug)
    {
        $category = Category::onlyTrashed()->where('slug', $slug)->firstOrFail();
        $category->restore();
        $category->products()->onlyTrashed()->restore();
        return redirect()->route('admin.categories.trash')->with('success', 'Đã khôi phục danh mục.');
    }

    public function forceDelete($slug)
    {
        $category = Category::onlyTrashed()->where('slug', $slug)->firstOrFail();
        $category->forceDelete();
        return redirect()->route('admin.categories.trash')->with('success', 'Đã xóa vĩnh viễn danh mục.');
    }

    public function trash(Request $request)
    {
        // Tabs thống kê
        $categoryAll = Category::withTrashed()->get();
        $categoryActive = Category::whereNull('deleted_at')->get();
        $categoryTrashed = Category::onlyTrashed()->get();

        $query = Category::onlyTrashed();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('min_date')) {
            $query->whereDate('deleted_at', '>=', $request->min_date);
        }
        if ($request->filled('max_date')) {
            $query->whereDate('deleted_at', '<=', $request->max_date);
        }

        $categories = $query->orderBy('deleted_at', 'DESC')->paginate(10);

        // Sử dụng lại view index cho đồng nhất tabs/filter/table
        return view('admin.categories.trash', [
            'categories' => $categories,
            'categoryAll' => $categoryAll,
            'categoryActive' => $categoryActive,
            'categoryTrashed' => $categoryTrashed,
            'title' => 'Thùng rác danh mục',
        ]);
    }

  public function show($slug, Request $request)
{
    $category = Category::where('slug', $slug)->firstOrFail();

    $productsQuery = $category->products()
        ->with(['productVariants.productVariantValues'])
        ->withTrashed();

    if ($request->filled('product_name')) {
        $productsQuery->where('name', 'like', '%' . $request->product_name . '%');
    }

    // Lọc giá dựa trên biến thể sản phẩm
    if ($request->filled('min_price') || $request->filled('max_price')) {
        $minPrice = $request->input('min_price', 0);
        $maxPrice = $request->input('max_price', PHP_INT_MAX);

        // Lấy danh sách product_id có biến thể nằm trong khoảng giá
        $productIds = \App\Models\ProductVariant::whereBetween('price', [$minPrice, $maxPrice])
            ->pluck('product_id')
            ->unique();

        // Lọc products theo danh sách product_id trên
        $productsQuery->whereIn('id', $productIds);
    }

    $products = $productsQuery->paginate(5);

    return view('admin.categories.show', compact('category', 'products'));
}

}
