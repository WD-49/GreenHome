<?php

namespace App\Http\Requests\admin\Banner;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'img' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên banner.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.boolean' => 'Trạng thái không hợp lệ.',
            'img.image' => 'Tệp tải lên phải là hình ảnh.',
            'img.mimes' => 'Hình ảnh phải có định dạng jpg, jpeg, png hoặc gif.',
            'img.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ];
    }
}
