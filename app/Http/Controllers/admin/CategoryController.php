<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Product; // Nhớ thêm dòng này để thao tác với Product
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

        // Filter theo tên (name)
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->name . '%')
                    ->orWhere('slug', 'like', '%' . $request->name . '%');
            });
        }

        // Filter trạng thái (1/0)
        if ($request->filled('status')) {
            $query->where('status', $request->status)
                ->whereNull('deleted_at');
        }

        // Filter ngày tạo
        if ($request->filled('min_date')) {
            $query->whereDate('created_at', '>=', $request->min_date);
        }
        if ($request->filled('max_date')) {
            $query->whereDate('created_at', '<=', $request->max_date);
        }

        // Số bản ghi mỗi trang
        $perPage = $request->get('per_page', 10);

        // Lấy danh sách danh mục + giữ tham số filter khi phân trang
        $categories = $query->orderBy('created_at', 'DESC')
            ->paginate($perPage)
            ->appends($request->all());

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
            'name.*' => ['required', 'string', 'max:255', 'distinct'],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string'],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'in:0,1'],
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.*.required' => 'Tên danh mục không được để trống.',
            'name.*.distinct' => 'Tên danh mục không được trùng.',
            'status.required' => 'Trạng thái là bắt buộc.',
        ]);

        if ($request->has('name') && is_array($request->name)) {
            $slugs = [];

            foreach ($request->name as $index => $name) {
                $slug = Str::slug($name);

                // Kiểm tra slug đã tồn tại trong database hoặc trùng trong mảng gửi lên
                if (Category::where('slug', $slug)->exists() || in_array($slug, $slugs)) {
                    return back()->withErrors(['name.' . $index => 'Danh mục "' . $name . '" đã tồn tại.'])->withInput();
                }

                // Lưu lại slug để kiểm tra các mục tiếp theo không trùng
                $slugs[] = $slug;
            }

            // Nếu không có slug nào bị trùng thì tiến hành lưu
            foreach ($request->name as $index => $name) {
                $slug = $slugs[$index];

                $category = new Category();
                $category->name = $name;
                $category->slug = $slug;
                $category->description = $request->description[$index] ?? null;
                $category->status = $request->status[$index] ?? 1;
                $category->save();
            }

            return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công.');
        } else {
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
            'status' => ['required', 'in:0,1'],
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'status.required' => 'Trạng thái là bắt buộc.',
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
        $category->status = $request->status;
        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $category->delete(); // chỉ xóa mềm category, không đụng đến sản phẩm
        return redirect()->route('admin.categories.index')->with('success', 'Đã chuyển danh mục vào thùng rác.');
    }

    public function restore($slug)
    {
        $category = Category::onlyTrashed()->where('slug', $slug)->firstOrFail();
        $category->restore(); // chỉ restore category, không đụng đến sản phẩm
        // XÓA DÒNG BÊN DƯỚI ĐỂ KHÔNG RESTORE SẢN PHẨM (nếu có)
        // $category->products()->onlyTrashed()->restore();
        return redirect()->route('admin.categories.trash')->with('success', 'Đã khôi phục danh mục.');
    }

    public function forceDelete($slug)
    {
        $category = Category::onlyTrashed()->where('slug', $slug)->firstOrFail();

        // Trước khi xóa vĩnh viễn, update category_id của sản phẩm về null
        Product::where('category_id', $category->id)->update(['category_id' => null]);

        $category->forceDelete();
        return redirect()->route('admin.categories.trash')->with('success', 'Đã xóa vĩnh viễn danh mục và các sản phẩm trong danh mục sẽ có danh mục trống.');
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

            $productIds = \App\Models\ProductVariant::whereBetween('price', [$minPrice, $maxPrice])
                ->pluck('product_id')
                ->unique();

            $productsQuery->whereIn('id', $productIds);
        }

        $products = $productsQuery->paginate(5);

        return view('admin.categories.show', compact('category', 'products'));
    }
}
