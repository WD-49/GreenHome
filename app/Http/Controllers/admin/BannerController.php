<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Http\Requests\Admin\Banner\StoreBannerRequest;
use App\Http\Requests\Admin\Banner\UpdateBannerRequest;
use Illuminate\Support\Facades\Storage;

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

    // Lọc theo khoảng ngày tạo
    if ($request->filled('min_date')) {
        $query->whereDate('created_at', '>=', $request->min_date);
    }
    if ($request->filled('max_date')) {
        $query->whereDate('created_at', '<=', $request->max_date);
    }

    $banners = $query->latest()->paginate(10);

    return view('admin.banners.index', compact('banners'));
}


    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(StoreBannerRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('img')) {
            $imagePath = $request->file('img')->store('images/banners', 'public');
            $data['img'] = 'storage/' . $imagePath;
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được thêm');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $data = $request->validated();

        if ($request->hasFile('img')) {
            if ($banner->img) {
                $oldImagePath = str_replace('storage/', '', $banner->img);
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }

            $imagePath = $request->file('img')->store('images/banners', 'public');
            $data['img'] = 'storage/' . $imagePath;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật thành công');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->img) {
            $imagePath = str_replace('storage/', '', $banner->img);
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Đã xóa banner');
    }
}
