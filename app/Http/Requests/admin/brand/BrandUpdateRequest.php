<?php

namespace App\Http\Requests\admin\brand;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str; 
use App\Models\Brand;

class BrandUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('brand');
        return [
            'name' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) use ($id) {
                    $slug = Str::slug($value);
                    $exists = Brand::where('slug', $slug)
                        ->where('id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        $fail('Tên thương hiệu đã tồn tại.');
                    }
                }
            ],
            'description' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên thương hiệu.',
            'name.max' => 'Tên thương hiệu không được vượt quá 20 ký tự.',
            'description.required' => 'Vui lòng nhập mô tả.',
        ];
    }
}
