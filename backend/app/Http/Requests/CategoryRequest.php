<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'image' => 'sometimes|nullable|string|max:255',
            'parent_id' => 'sometimes|integer|min:0|max:4294967295',
            'sort_order' => 'sometimes|integer|min:0|max:4294967295',
            'description' => 'sometimes|nullable|string',
            'created_by' => 'sometimes|integer|min:0|max:4294967295',
            'updated_by' => 'sometimes|nullable|integer|min:0|max:4294967295',
            'status' => 'required|integer|min:0|max:255',
        ];
    }
}
