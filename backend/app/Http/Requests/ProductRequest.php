<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'category_id' => [
                'required',
                'integer',
                'exists:danhmuc,id',
            ],

            'brand_id' => [
                'required',
                'integer',
                'exists:thuonghieu,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sanpham', 'name')->ignore($id),
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sanpham', 'slug')->ignore($id),
            ],

            'price_buy' => 'required|numeric|min:0',

            'price_sale' => 'nullable|numeric|min:0',

            'image' => 'required|string|max:255',

            'qty' => 'required|integer|min:0',

            'detail' => 'required|string',

            'description' => 'nullable|string',

            'status' => 'required|integer',

            'sku' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('sanpham', 'sku')->ignore($id),
            ],

            'unit' => 'nullable|string|max:30',

            'prep_minutes' => 'nullable|integer|min:0',

            'is_available' => 'nullable|boolean',

            'is_featured' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Danh muc khong duoc de trong.',
            'category_id.integer' => 'Danh muc phai la so nguyen.',
            'category_id.exists' => 'Danh muc khong ton tai.',

            'brand_id.required' => 'Thuong hieu khong duoc de trong.',
            'brand_id.integer' => 'Thuong hieu phai la so nguyen.',
            'brand_id.exists' => 'Thuong hieu khong ton tai.',

            'name.required' => 'Ten san pham khong duoc de trong.',
            'name.string' => 'Ten san pham phai la chuoi.',
            'name.max' => 'Ten san pham khong duoc vuot qua 255 ky tu.',
            'name.unique' => 'Ten san pham da ton tai, khong duoc tao trung.',

            'slug.required' => 'Slug khong duoc de trong.',
            'slug.string' => 'Slug phai la chuoi.',
            'slug.max' => 'Slug khong duoc vuot qua 255 ky tu.',
            'slug.unique' => 'Slug san pham da ton tai.',

            'price_buy.required' => 'Gia nhap khong duoc de trong.',
            'price_buy.numeric' => 'Gia nhap phai la so.',
            'price_buy.min' => 'Gia nhap khong duoc nho hon 0.',

            'price_sale.numeric' => 'Gia ban phai la so.',
            'price_sale.min' => 'Gia ban khong duoc nho hon 0.',

            'image.required' => 'Hinh anh khong duoc de trong.',

            'qty.required' => 'So luong khong duoc de trong.',
            'qty.integer' => 'So luong phai la so nguyen.',
            'qty.min' => 'So luong khong duoc nho hon 0.',

            'detail.required' => 'Chi tiet san pham khong duoc de trong.',

            'status.required' => 'Trang thai khong duoc de trong.',
            'status.integer' => 'Trang thai phai la so nguyen.',

            'sku.unique' => 'Ma SKU da ton tai.',
            'sku.max' => 'Ma SKU khong duoc vuot qua 50 ky tu.',

            'unit.max' => 'Don vi khong duoc vuot qua 30 ky tu.',

            'prep_minutes.integer' => 'Thoi gian chuan bi phai la so nguyen.',
            'prep_minutes.min' => 'Thoi gian chuan bi khong duoc nho hon 0.',

            'is_available.boolean' => 'Trang thai ban phai la true hoac false.',

            'is_featured.boolean' => 'Trang thai noi bat phai la true hoac false.',
        ];
    }
}