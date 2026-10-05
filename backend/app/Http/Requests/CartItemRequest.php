<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => $this->isMethod('post') ? 'required|integer|exists:sanpham,id' : 'prohibited',
            'quantity' => 'required|integer|min:1|max:999',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Vui long chon san pham.',
            'product_id.exists' => 'San pham khong ton tai.',
            'quantity.required' => 'Vui long nhap so luong.',
            'quantity.integer' => 'So luong phai la so nguyen.',
            'quantity.min' => 'So luong it nhat la 1.',
            'quantity.max' => 'So luong toi da la 999.',
        ];
    }
}
