<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\attribute\StoreAttributeRequest;
use App\Http\Requests\admin\attribute\UpdateAttributeRequest;
use App\Models\Attribute;
use App\Models\attributeValue;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
   public function index()
   {
      $title = "Quản lý thuộc tính";
      $attributes = Attribute::all();
      return view('admin.attribute.index', compact('title', 'attributes'));
   }
   public function create()
   {
      $title = "Thêm thuộc tính mới";
      return view('admin.attribute.create', compact('title'));
   }
   public function store(StoreAttributeRequest $storeAttributeRequest)
   {
      $data = $storeAttributeRequest->validated();
      Attribute::insert([
         'name' => $data['name']
      ]);
      return redirect()->route('admin.attribute.index')->with('success', 'Thêm thuộc tính thành công!');
   }
   public function edit($id)
   {
      $title = "Chỉnh sửa thuộc tính";
      $attribute = Attribute::findOrFail($id);
      return view('admin.attribute.edit', compact('title', 'attribute'));
   }
   public function update($id, UpdateAttributeRequest $updateAttributeRequest)
   {
      $data = $updateAttributeRequest->validated();
      $attribute = Attribute::findOrFail($id);
      $attribute->update([
         'name' => $data['name']
      ]);
      return redirect()->route('admin.attribute.index')->with('success', 'Sửa thuộc tính thành công!');
   }
   public function show($id)
   {
      $attribute = Attribute::findOrFail($id);
      $attributeValues = attributeValue::where('attribute_id', $id)->get();

      return view('admin.attribute.show', compact('attribute', 'attributeValues'));
   }
   public function destroy($id)
   {
      $attribute = Attribute::findOrFail($id);
      $attribute->delete();
      return redirect()->back()->with('success', 'Xóa tạm thuộc tính thành công!');
   }
   public function trash()
   {
      $attributes = Attribute::onlyTrashed()->get();
      return view('admin.attribute.trash', compact('attributes'));
   }
   public function restore($id)
   {
      $attribute = Attribute::onlyTrashed()->findOrFail($id);
      $attribute->restore();
      return redirect()->route('admin.attribute.trash')
         ->with('success', 'Phục hồi thuộc tính thành công!');
   }
}
