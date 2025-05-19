<?php

namespace App\Http\Requests\admin\attribute\value;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeValueRequest extends FormRequest
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
        'attribute_id' => 'required|exists:attributes,id',
        'name' => 'required|string|max:255|unique:attribute_values,value',
    ];
}

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập giá trị.',
            'name.string' => 'Giá trị phải là chuỗi.',
            'name.max' => 'Giá trị tối đa 255 ký tự.',
        ];
    }

}
