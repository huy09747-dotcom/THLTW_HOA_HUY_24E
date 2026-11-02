<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:nguoidung,email',
            'phone' => 'required|string|max:255|unique:nguoidung,phone',
            'username' => 'sometimes|nullable|string|max:255',
            'password' => 'required|string|min:8|max:255|confirmed',
            'address' => 'sometimes|nullable|string|max:255',
        ];
    }
}
