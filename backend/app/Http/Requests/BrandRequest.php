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
            'name.required' => 'Ten thuong hieu khong duoc de trong.',
            'name.string' => 'Ten thuong hieu phai la chuoi.',
            'name.max' => 'Ten thuong hieu khong duoc vuot qua 255 ky tu.',
            'name.unique' => 'Ten thuong hieu da ton tai, khong duoc tao trung.',

            'slug.required' => 'Slug khong duoc de trong.',
            'slug.string' => 'Slug phai la chuoi.',
            'slug.max' => 'Slug khong duoc vuot qua 255 ky tu.',
            'slug.unique' => 'Slug thuong hieu da ton tai.',

            'sort_order.integer' => 'Thu tu phai la so nguyen.',
            'sort_order.min' => 'Thu tu khong duoc nho hon 0.',

            'description.required' => 'Mo ta khong duoc de trong.',

            'content.required' => 'Noi dung khong duoc de trong.',

            'status.required' => 'Trang thai khong duoc de trong.',
            'status.integer' => 'Trang thai phai la so nguyen.',
        ];
    }
}