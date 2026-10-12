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

            'price_buy' => 'required|integer|min:1000|multiple_of:1000',

            'price_sale' => 'required|integer|min:1000|multiple_of:1000|gte:price_buy',
            'discount_percent' => 'sometimes|integer|min:0|max:100',

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

            'unit' => ['required', Rule::in(['Cái', 'Chiếc', 'Bộ', 'Hộp'])],

            'prep_minutes' => 'nullable|integer|min:0',

            'is_available' => 'nullable|boolean',

            'is_featured' => 'nullable|boolean',
        ];
    }

    public function after(): array
    {
        return [function ($validator) {
            if ($validator->errors()->isNotEmpty()) return;
            $product = $this->route('id')
                ? \App\Models\Product::find($this->route('id'))
                : null;
            $percent = (int) $this->input('discount_percent', $product?->discount_percent ?? 0);
            $final = (int) round((int) $this->input('price_sale') * (100 - $percent) / 100);
            if ($final < (int) $this->input('price_buy')) {
                $validator->errors()->add('discount_percent', 'Gia sau khuyen mai khong duoc thap hon gia nhap.');
            }
        }];
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
            'price_buy.min' => 'Gia nhap toi thieu 1.000 dong.',
            'price_buy.multiple_of' => 'Gia nhap phai la boi so cua 1.000 dong.',

            'price_sale.required' => 'Vui long nhap gia ban.',
            'price_sale.gte' => 'Gia ban khong duoc thap hon gia nhap.',
            'discount_percent.integer' => 'Khuyen mai phai la so nguyen.',
            'discount_percent.min' => 'Khuyen mai tu 0 den 100%.',
            'discount_percent.max' => 'Khuyen mai tu 0 den 100%.',
            'unit.in' => 'Don vi phai la Cai, Chiec, Bo hoac Hop.',
            'unit.required' => 'Vui long chon don vi.',
            'price_sale.numeric' => 'Gia ban phai la so.',
            'price_sale.min' => 'Gia ban toi thieu 1.000 dong.',
            'price_sale.multiple_of' => 'Gia ban phai la boi so cua 1.000 dong.',

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