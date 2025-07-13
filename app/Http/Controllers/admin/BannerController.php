<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Banner\StoreBannerRequest;
use App\Http\Requests\Admin\Banner\UpdateBannerRequest;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();

        // Tìm theo tên banner
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Lọc theo trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo ngày tạo
        if ($request->filled('min_date')) {
            $query->whereDate('created_at', '>=', $request->min_date);
        }
        if ($request->filled('max_date')) {
            $query->whereDate('created_at', '<=', $request->max_date);
        }

       $banners = $query->with('category')->latest()->paginate(10);


        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.banners.create', compact('categories'));
    }

    public function store(StoreBannerRequest $request)
    {
        $data = $request->validated();

        // Xử lý ảnh
        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('images/banners', 'public');
            $data['img'] = 'storage/' . $path;
        }
        //  dd($data);
        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được thêm thành công');
    }

    public function edit(Banner $banner)
    {
        $categories = Category::where('status', 1)->get();
        return view('admin.banners.edit', compact('banner', 'categories'));
    }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $data = $request->validated();

        // Xử lý ảnh mới
        if ($request->hasFile('img')) {
            if ($banner->img) {
                $oldPath = str_replace('storage/', '', $banner->img);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $path = $request->file('img')->store('images/banners', 'public');
            $data['img'] = 'storage/' . $path;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật banner thành công');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        // Xóa hình ảnh nếu có
        if ($banner->img) {
            $imgPath = str_replace('storage/', '', $banner->img);
            if (Storage::disk('public')->exists($imgPath)) {
                Storage::disk('public')->delete($imgPath);
            }
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Đã xóa banner thành công');
    }
}
