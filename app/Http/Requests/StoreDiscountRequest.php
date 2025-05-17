<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscountRequest extends FormRequest
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
        'title' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'code' => 'required|string|max:255|unique:discounts,code',
        'discount_type' => 'required|in:percentage,fixed',
        'discount_value' => 'required|numeric|min:1',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'max_discount' => 'required|numeric|min:0',
        'min_order_value' => 'required|numeric|min:0',
        'quantity' => 'required|integer|min:1',
        'user_usage_limit' => 'required|integer|min:1',
        'applies_to_all_products' => 'required|boolean',
        'status' => 'required|in:active,inactive',
    ];
}

//     public function messages(): array
// {
//     return [
//         'code.required' => 'Vui lòng nhập mã giảm giá.',
//         'code.max' => 'Mã giảm giá không được vượt quá 255 ký tự.',
//         'value.required' => 'Vui lòng nhập giá trị giảm.',
//         'value.numeric' => 'Giá trị giảm phải là số.',
//         'value.min' => 'Giá trị giảm phải lớn hơn 0.',
//         'type.required' => 'Vui lòng chọn loại giảm.',
//         'type.in' => 'Loại giảm không hợp lệ.',
//         'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
//         'start_date.date' => 'Ngày bắt đầu không hợp lệ.',
//         'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
//         'end_date.date' => 'Ngày kết thúc không hợp lệ.',
//         'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',
//     ];
// }
public function messages(): array
{
    return [
        'code.required' => 'Vui lòng nhập mã giảm giá.',
        'code.max' => 'Mã giảm giá không được vượt quá 255 ký tự.',

     'discount_value.required' => 'Vui lòng nhập giá trị giảm.',
'discount_value.numeric' => 'Giá trị giảm phải là số.',

        'discount_value.min' => 'Giá trị giảm phải lớn hơn 0.',

        'discount_type.required' => 'Vui lòng chọn loại giảm.',
        'discount_type.in' => 'Loại giảm không hợp lệ.',

        'start_date.required' => 'Vui lòng chọn ngày bắt đầu.',
        'start_date.date' => 'Ngày bắt đầu không hợp lệ.',

        'end_date.required' => 'Vui lòng chọn ngày kết thúc.',
        'end_date.date' => 'Ngày kết thúc không hợp lệ.',
        'end_date.after' => 'Ngày kết thúc phải sau ngày bắt đầu.',

        // Bạn có thể thêm các message cho các trường khác nếu muốn
    ];
}

}
