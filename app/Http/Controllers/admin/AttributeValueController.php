<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\admin\attribute\value\StoreAttributeValueRequest;
use App\Http\Requests\admin\attribute\value\UpdateAttributeValueRequest;
use App\Models\Attribute;
use App\Models\attributeValue;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    public function index()
    {
        $attributeValues = attributeValue::with('attribute')->paginate(10);
        $title = "Quản lý giá trị thuộc tính";
        return view('admin.attribute.value.index', compact('title', 'attributeValues'));
    }
    public function create()
    {
        $title = "Thêm giá trị";
        $attributes = Attribute::all();
        return view('admin.attribute.value.create', compact('title', 'attributes'));
    }
    public function store(StoreAttributeValueRequest $request)
    {
        $data = $request->validated();
        AttributeValue::create([
            'attribute_id' => $data['attribute_id'],
            'value' => $data['name'],
        ]);
        return redirect()->route('admin.attribute.value.index')
            ->with('success', 'Thêm giá trị thuộc tính thành công!');
    }
    public function edit($id)
    {
        $title = "Chỉnh sửa giá trị thuộc tính";
        $attributeValue = attributeValue::findOrFail($id);
        return view('admin.attribute.value.edit', compact('title', 'attributeValue'));
    }
    public function update($id, UpdateAttributeValueRequest $request)
    {
        $data = $request->validated();
        $value = attributeValue::findOrFail($id);
        $value->update([
            'value' => $data['value']
        ]);
        return redirect()->route('admin.attribute.value.index')->with('success', 'Thêm giá trị thuộc tính thành công!');
    }
    public function trash()
    {
        $values = AttributeValue::onlyTrashed()->with('attribute')->get();
        return view('admin.attribute.value.trash', compact('values'));
    }
    public function restore($id)
    {
        $value = AttributeValue::onlyTrashed()->findOrFail($id);
        $value->restore();
        return redirect()->route('admin.attribute.value.trash')->with('success', 'Khôi phục thành công!');
    }
    public function destroy($id)
        {
            $attribute = attributeValue::findOrFail($id);
            $attribute->delete();
            return redirect()->back()->with('success', 'Xóa tạm giá thành công!');
        }
}
