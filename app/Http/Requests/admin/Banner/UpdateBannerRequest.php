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
            'name' => 'nullable|string|max:255', // Không bắt buộc
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'priority' => 'required|integer',
            'status' => 'required|boolean',
            'img' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Bắt buộc
            'type' => 'required|string|in:slider,category_banner,discount_banner',
            'category_id' => 'nullable|exists:categories,id',
        ];
    }



    public function messages(): array
    {
        return [
            'name.string' => 'Tên banner phải là chuỗi.',
            'name.max' => 'Tên banner tối đa 255 ký tự.',

            'description.string' => 'Mô tả phải là chuỗi.',

            'link.url' => 'Liên kết không hợp lệ.',

            'priority.required' => 'Vui lòng nhập thứ tự ưu tiên.',
            'priority.integer' => 'Thứ tự ưu tiên phải là số nguyên.',

            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.boolean' => 'Trạng thái không hợp lệ.',

            'img.image' => 'Tệp tải lên phải là hình ảnh.',
            'img.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, gif hoặc webp.',
            'img.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',

            'type.required' => 'Vui lòng chọn loại banner.',
            'type.string' => 'Loại banner phải là chuỗi.',
            'type.in' => 'Loại banner không hợp lệ.',

            'category_id.exists' => 'Danh mục đã chọn không tồn tại.',
        ];
    }
}
