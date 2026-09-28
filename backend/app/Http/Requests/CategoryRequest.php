<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
                Rule::unique('danhmuc', 'name')->ignore($id),
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('danhmuc', 'slug')->ignore($id),
            ],

            'image' => 'sometimes|nullable|string|max:255',
            'parent_id' => 'sometimes|integer|min:0|max:4294967295',
            'sort_order' => 'sometimes|integer|min:0|max:4294967295',
            'description' => 'sometimes|nullable|string',
            'created_by' => 'sometimes|integer|min:0|max:4294967295',
            'updated_by' => 'sometimes|nullable|integer|min:0|max:4294967295',
            'status' => 'required|integer|min:0|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên danh mục không được để trống.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'slug.required' => 'Slug không được để trống.',
            'slug.unique' => 'Slug đã tồn tại.',
            'status.required' => 'Trạng thái không được để trống.',
        ];
    }
}