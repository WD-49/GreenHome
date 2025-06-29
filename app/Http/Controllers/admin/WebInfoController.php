<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebInfoController extends Controller
{
    public function show()
    {
        $webInfos = DB::table('web_infos')->pluck('value', 'key');
        return view('admin.webinfor.show', compact('webInfos'));
    }

    public function edit()
    {
        $webInfos = DB::table('web_infos')->pluck('value', 'key');
        return view('admin.webinfor.edit', compact('webInfos'));
    }

    public function update(Request $request)
    {
        $data = $request->only(['web_name', 'email', 'phone', 'address', 'sortDes']);

        foreach ($data as $key => $value) {
            DB::table('web_infos')->updateOrInsert(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('admin.web_info.show')->with('success', 'Cập nhật thông tin website thành công!');
    }
}
