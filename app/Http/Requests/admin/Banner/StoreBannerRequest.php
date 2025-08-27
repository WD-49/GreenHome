<?php

namespace App\Http\Requests\admin\Banner;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'nullable|string|max:255', // Không bắt buộc
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'priority' => 'nullable|integer',
            'status' => 'required|boolean',
            'img' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Bắt buộc
            'type' => 'required|string|in:slider,category_banner,discount_banner',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }




    public function messages(): array
    {
        return [
            'name.max' => 'Tên không được vượt quá 255 ký tự.',

            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.boolean' => 'Trạng thái không hợp lệ.',

            'img.required' => 'Vui lòng chọn hình ảnh cho banner.',
            'img.image' => 'Tệp tải lên phải là hình ảnh.',
            'img.mimes' => 'Hình ảnh phải có định dạng jpg, jpeg, png hoặc gif.',
            'img.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
        ];
    }
}
