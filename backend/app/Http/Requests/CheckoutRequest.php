<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->status === 1;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['sometimes', 'required', 'in:cod,payos'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^\\+?[0-9]{9,15}$/'],
            'email' => ['required', 'email', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            '*.required' => 'Vui long nhap :attribute.',
            '*.string' => ':attribute phai la chuoi.',
            '*.max' => ':attribute khong duoc vuot qua :max ky tu.',
            'phone.regex' => 'So dien thoai phai co 9 den 15 chu so.',
            'email.email' => 'Email khong hop le.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'ho ten', 'phone' => 'so dien thoai',
            'email' => 'email', 'address' => 'dia chi', 'note' => 'ghi chu',
        ];
    }
}
