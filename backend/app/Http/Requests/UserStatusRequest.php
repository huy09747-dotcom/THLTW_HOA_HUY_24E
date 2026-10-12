<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->roles === 'admin' && $this->user()?->status === 1;
    }

    public function rules(): array
    {
        return ['status' => ['required', 'integer', Rule::in([0, 1])]];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Vui long chon trang thai tai khoan.',
            'status.integer' => 'Trang thai tai khoan khong hop le.',
            'status.in' => 'Trang thai tai khoan chi nhan 0 hoac 1.',
        ];
    }
}
