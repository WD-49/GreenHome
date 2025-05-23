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
    // Hiển thị danh sách banner, phân trang 10 bản ghi
    public function index()
    {
        $banners = Banner::latest()->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    // Form tạo mới banner
    public function create()
    {
        return view('admin.banners.create');
    }

    // Lưu banner mới
    public function store(StoreBannerRequest $request)
    {
        $data = $request->validated();

        // Xử lý upload ảnh nếu có
        if ($request->hasFile('img')) {
            $imagePath = $request->file('img')->store('images/banners', 'public');
            // Lưu đường dẫn theo chuẩn để hiển thị trên web: storage/...
            $data['img'] = 'storage/' . $imagePath;
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner đã được thêm');
    }

    // Form chỉnh sửa banner
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    // Cập nhật banner
    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $data = $request->only('name', 'status');

        if ($request->hasFile('img')) {
            // Xóa ảnh cũ nếu có
            if ($banner->img) {
                $oldImagePath = str_replace('storage/', '', $banner->img);
                if (Storage::disk('public')->exists($oldImagePath)) {
                    Storage::disk('public')->delete($oldImagePath);
                }
            }

            // Lưu ảnh mới
            $imagePath = $request->file('img')->store('images/banners', 'public');
            $data['img'] = 'storage/' . $imagePath;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Cập nhật thành công');
    }

    // Xóa banner
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        // Xóa ảnh nếu có
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
