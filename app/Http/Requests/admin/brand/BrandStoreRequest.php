<?php

namespace App\Http\Requests\admin\brand;

use Illuminate\Foundation\Http\FormRequest;

class BrandStoreRequest extends FormRequest
{
      public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */




   public function rules(): array
{
    return [
        'brands' => 'required|array|min:1',
        'brands.*.name' => 'required|string|max:20|distinct|unique:brands,name',
        'brands.*.description' => 'required|string',
    ];
}


public function messages(): array
{
    return [
        'brands.required' => 'Vui lòng thêm ít nhất một thương hiệu.',
        'brands.*.name.required' => 'Vui lòng nhập tên thương hiệu.',
        'brands.*.name.max' => 'Tên thương hiệu không được vượt quá 20 ký tự.',
        'brands.*.name.unique' => 'Tên thương hiệu đã tồn tại.',
        'brands.*.name.distinct' => 'Tên thương hiệu bị trùng lặp trong danh sách.',
        'brands.*.description.required' => 'Vui lòng nhập mô tả thương hiệu.',
    ];
}


}
