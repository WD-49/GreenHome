<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Http\Requests\Admin\Banner\StoreBannerRequest;
use App\Http\Requests\Admin\Banner\UpdateBannerRequest;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::latest()->paginate(10);
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
        $file = $request->file('img');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/banners'), $filename);
        $data['img'] = 'uploads/banners/' . $filename;
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
    $data = $request->only('name', 'status');

    if ($request->hasFile('img')) {
        // Xóa ảnh cũ nếu tồn tại
        if ($banner->img && file_exists(public_path($banner->img))) {
            unlink(public_path($banner->img));
        }

        $file = $request->file('img');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/banners'), $filename);
        $data['img'] = 'uploads/banners/' . $filename;
    }

    $banner->update($data);

    return redirect()->route('admin.banners.index')->with('success', 'Cập nhật thành công');
}

public function destroy(Banner $banner)
{
    // Xóa file ảnh nếu tồn tại
    if ($banner->img && file_exists(public_path($banner->img))) {
        unlink(public_path($banner->img));
    }

    $banner->delete();

    return redirect()->route('admin.banners.index')->with('success', 'Đã xóa banner');
}

}
