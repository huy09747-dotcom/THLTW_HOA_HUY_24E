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
            'category_id.required' => 'Danh mục không được để trống.',
            'category_id.integer' => 'Danh mục phải là số nguyên.',
            'category_id.exists' => 'Danh mục không tồn tại.',

            'brand_id.required' => 'Thương hiệu không được để trống.',
            'brand_id.integer' => 'Thương hiệu phải là số nguyên.',
            'brand_id.exists' => 'Thương hiệu không tồn tại.',

            'name.required' => 'Tên sản phẩm không được để trống.',
            'name.string' => 'Tên sản phẩm phải là chuỗi.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên sản phẩm đã tồn tại, không được tạo trùng.',

            'slug.required' => 'Slug không được để trống.',
            'slug.string' => 'Slug phải là chuỗi.',
            'slug.max' => 'Slug không được vượt quá 255 ký tự.',
            'slug.unique' => 'Slug sản phẩm đã tồn tại.',

            'price_buy.required' => 'Giá nhập không được để trống.',
            'price_buy.numeric' => 'Giá nhập phải là số.',
            'price_buy.min' => 'Giá nhập không được nhỏ hơn 0.',

            'price_sale.numeric' => 'Giá bán phải là số.',
            'price_sale.min' => 'Giá bán không được nhỏ hơn 0.',

            'image.required' => 'Hình ảnh không được để trống.',

            'qty.required' => 'Số lượng không được để trống.',
            'qty.integer' => 'Số lượng phải là số nguyên.',
            'qty.min' => 'Số lượng không được nhỏ hơn 0.',

            'detail.required' => 'Chi tiết sản phẩm không được để trống.',

            'status.required' => 'Trạng thái không được để trống.',
            'status.integer' => 'Trạng thái phải là số nguyên.',

            'sku.unique' => 'Mã SKU đã tồn tại.',
            'sku.max' => 'Mã SKU không được vượt quá 50 ký tự.',

            'unit.max' => 'Đơn vị không được vượt quá 30 ký tự.',

            'prep_minutes.integer' => 'Thời gian chuẩn bị phải là số nguyên.',
            'prep_minutes.min' => 'Thời gian chuẩn bị không được nhỏ hơn 0.',

            'is_available.boolean' => 'Trạng thái bán phải là true hoặc false.',

            'is_featured.boolean' => 'Trạng thái nổi bật phải là true hoặc false.',
        ];
    }
}