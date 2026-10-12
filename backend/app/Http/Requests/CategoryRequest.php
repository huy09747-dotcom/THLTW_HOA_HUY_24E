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
            'parent_id' => [
                'sometimes', 'integer', 'min:0', 'max:4294967295',
                function ($attribute, $value, $fail) use ($id) {
                    $seen = [];
                    $parentId = (int) $value;
                    while ($parentId !== 0) {
                        if ($parentId === (int) $id || isset($seen[$parentId])) {
                            $fail('Danh muc cha khong duoc la chinh no hoac danh muc con.');
                            return;
                        }
                        $seen[$parentId] = true;
                        $parent = \App\Models\Category::find($parentId);
                        if (! $parent) {
                            $fail('Danh muc cha khong ton tai.');
                            return;
                        }
                        $parentId = (int) $parent->parent_id;
                    }
                },
            ],
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
            'name.required' => 'Ten danh muc khong duoc de trong.',
            'name.unique' => 'Ten danh muc da ton tai.',
            'slug.required' => 'Slug khong duoc de trong.',
            'slug.unique' => 'Slug da ton tai.',
            'status.required' => 'Trang thai khong duoc de trong.',
        ];
    }
}