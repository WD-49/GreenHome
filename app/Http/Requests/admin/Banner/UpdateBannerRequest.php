<?php

namespace App\Http\Requests\admin\Banner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

public function rules()
{
    return [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'link' => 'nullable|url',
        'priority' => 'nullable|integer',
        'status' => 'required|boolean',
        'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ];
}



    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên banner.',
            'name.string' => 'Tên banner phải là chuỗi.',
            'name.max' => 'Tên banner tối đa 255 ký tự.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.boolean' => 'Trạng thái không hợp lệ.',
            'img.image' => 'Tệp tải lên phải là hình ảnh.',
            'img.mimes' => 'Hình ảnh phải có định dạng jpg, jpeg, png hoặc gif.',
            'img.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ];
    }
}
