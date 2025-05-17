<?php

namespace App\Http\Requests\admin\attribute\value;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttributeValueRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
        'value' => 'required|string|max:255|unique:attribute_values,value',
    ];
}

    public function messages(): array
    {
        return [
            'value.required' => 'Vui lòng nhập giá trị.',
            'value.string' => 'Giá trị phải là chuỗi.',
            'value.max' => 'Giá trị tối đa 255 ký tự.',
        ];
    }
}
