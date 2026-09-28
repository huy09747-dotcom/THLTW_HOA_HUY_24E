<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('thuonghieu', 'name')->ignore($id),
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('thuonghieu', 'slug')->ignore($id),
            ],

            'sort_order' => 'nullable|integer|min:0',

            'description' => 'required|string',

            'image' => 'nullable|string|max:255',

            'title' => 'nullable|string|max:255',

            'content' => 'required|string',

            'status' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên thương hiệu không được để trống.',
            'name.string' => 'Tên thương hiệu phải là chuỗi.',
            'name.max' => 'Tên thương hiệu không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên thương hiệu đã tồn tại, không được tạo trùng.',

            'slug.required' => 'Slug không được để trống.',
            'slug.string' => 'Slug phải là chuỗi.',
            'slug.max' => 'Slug không được vượt quá 255 ký tự.',
            'slug.unique' => 'Slug thương hiệu đã tồn tại.',

            'sort_order.integer' => 'Thứ tự phải là số nguyên.',
            'sort_order.min' => 'Thứ tự không được nhỏ hơn 0.',

            'description.required' => 'Mô tả không được để trống.',

            'content.required' => 'Nội dung không được để trống.',

            'status.required' => 'Trạng thái không được để trống.',
            'status.integer' => 'Trạng thái phải là số nguyên.',
        ];
    }
}