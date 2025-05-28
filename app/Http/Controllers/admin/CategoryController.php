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
        $query = Category::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status == 'active') {
            $query->whereNull('deleted_at'); // Chỉ bản ghi chưa bị xóa
        }

        $categories = $query->orderBy('created_at', 'DESC')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        // Validate tên danh mục và slug duy nhất
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
        ]);

        // Tạo slug tự động từ tên danh mục và đảm bảo tính duy nhất
        $slug = Str::slug($request->name);
        $existingSlug = Category::where('slug', $slug)->first();

        if ($existingSlug) {
            $slug = $slug . '-' . uniqid();  // Thêm hậu tố với ID ngẫu nhiên
        }

        $category = new Category($request->all());
        $category->slug = $slug;
        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Thêm danh mục thành công.');
    }

    public function edit($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();  // Tìm bằng slug
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();  // Tìm bằng slug

        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
        ]);

        $slug = Str::slug($request->name);
        $existingSlug = Category::where('slug', $slug)->where('id', '!=', $category->id)->first();

        if ($existingSlug) {
            $slug = $slug . '-' . uniqid();
        }

        $category->name = $request->name;
        $category->description = $request->description;
        $category->slug = $slug;
        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật danh mục thành công.');
    }

    public function destroy($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();  // Tìm bằng slug
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Đã chuyển danh mục vào thùng rác.');
    }

    public function restore($slug)
    {
        $category = Category::onlyTrashed()->where('slug', $slug)->firstOrFail();  // Tìm bằng slug
        $category->restore();
        $category->products()->onlyTrashed()->restore();
        return redirect()->route('admin.categories.trash')->with('success', 'Đã khôi phục danh mục.');
    }

    public function forceDelete($slug)
    {
        $category = Category::onlyTrashed()->where('slug', $slug)->firstOrFail();  // Tìm bằng slug
        $category->forceDelete();
        return redirect()->route('admin.categories.trash')->with('success', 'Đã xóa vĩnh viễn danh mục.');
    }
    public function trash(Request $request)
{
    $query = Category::onlyTrashed();  // Retrieve only trashed categories

    if ($request->has('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Paginate the results
    $categories = $query->orderBy('deleted_at', 'DESC')->paginate(10);

    return view('admin.categories.trash', compact('categories'));
}
public function show($slug)
{
    // Find the category by slug
    $category = Category::where('slug', $slug)->firstOrFail();

    // Return the view with the category data
    return view('admin.categories.show', compact('category'));
}
}

