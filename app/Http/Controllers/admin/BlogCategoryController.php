<?php
namespace App\Http\Controllers\Admin;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
   public function index(Request $request)
    {
        // Đếm cho tabs
        $all = BlogCategory::withTrashed()->get();
        $active = BlogCategory::whereNull('deleted_at')->get();
        $Trashed = BlogCategory::onlyTrashed()->get();

        $query = BlogCategory::query();

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

        return view('admin.blog_categories.index', [
            'categories' => $categories,
            'all' => $all,
            'active' => $active,
            'Trashed' => $Trashed,
            'title' => 'Danh sách danh mục',
        ]);
    }


    // Các method còn lại như: create, store, edit, update, destroy, restore, forceDelete, trash, show
            // Copy từ CategoryController và thay thế Category -> BlogCategory
            public function create()
        {
            return view('admin.blog_categories.create', [
                'title' => 'Thêm danh mục blog mới',
            ]);
        }

        public function store(Request $request)
        {
            $data = $request->validate([
                'name' => 'required|string|max:255|unique:blog_categories,name',
                'description' => 'nullable|string',
            ]);

            $data['slug'] = Str::slug($data['name']);

            BlogCategory::create($data);

            return redirect()->route('admin.blog_categories.index')
                ->with('success', 'Thêm danh mục blog thành công.');
        }

        public function edit($slug)
        {
            $category = BlogCategory::withTrashed()->where('slug', $slug)->firstOrFail();

            return view('admin.blog_categories.edit', [
                'category' => $category,
                'title' => 'Chỉnh sửa danh mục blog',
            ]);
        }

        public function update(Request $request, $slug)
        {
            $category = BlogCategory::withTrashed()->where('slug', $slug)->firstOrFail();

            $data = $request->validate([
                'name' => 'required|string|max:255|unique:blog_categories,name,' . $category->id,
                'description' => 'nullable|string',
            ]);

            $data['slug'] = Str::slug($data['name']);

            $category->update($data);

            return redirect()->route('admin.blog_categories.index')
                ->with('success', 'Cập nhật danh mục blog thành công.');
        }
        
        public function destroy($slug)
        {
            $category = BlogCategory::where('slug', $slug)->firstOrFail();
            $category->delete();

            return redirect()->back()->with('success', 'Danh mục đã được đưa vào thùng rác.');
        }
   public function trash(Request $request)
    {
        // Tabs thống kê
        $categoryAll = BlogCategory::withTrashed()->get();
        $categoryActive = BlogCategory::whereNull('deleted_at')->get();
        $categoryTrashed = BlogCategory::onlyTrashed()->get();

        $query = BlogCategory::onlyTrashed();

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
        return view('admin.blog_categories.trash', [
            'categories' => $categories,
            'categoryAll' => $categoryAll,
            'categoryActive' => $categoryActive,
            'categoryTrashed' => $categoryTrashed,
            'title' => 'Thùng rác danh mục',
        ]);
    }


        public function restore($slug)
        {
            $category = BlogCategory::onlyTrashed()->where('slug', $slug)->firstOrFail();
            $category->restore();

            return redirect()->back()->with('success', 'Khôi phục danh mục thành công.');
        }
        
        public function forceDelete($slug)
        {
            $category = BlogCategory::onlyTrashed()->where('slug', $slug)->firstOrFail();
            $category->forceDelete();

            return redirect()->back()->with('success', 'Đã xóa vĩnh viễn danh mục.');
        }
   public function show($slug)
{
    $category = BlogCategory::with(['blogs' => function ($query) {
        $query->withTrashed(); // Nếu có soft delete sản phẩm, có thể bỏ nếu không dùng
    }])->where('slug', $slug)->firstOrFail();

    $blogs = $category->blogs;
    $blogCount = $blogs->count();

    return view('admin.blog_categories.show', compact('category', 'blogs', 'blogCount'));
}



}
