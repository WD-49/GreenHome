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
        'name.required' => 'Vui lòng nhập tên thương hiệu.',
        'name.max' => 'Tên thương hiệu không được vượt quá 20 ký tự.',
        'name.unique' => 'Tên thương hiệu đã tồn tại.',
        'description.required' => 'Vui lòng nhập mô tả.',
    ];
}

}
