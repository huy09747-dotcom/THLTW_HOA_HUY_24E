<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->roles === 'admin' && $this->user()?->status === 1;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('baiviet', 'slug')->ignore($this->route('id'))],
            'topic_id' => ['nullable', 'integer', Rule::exists('chudebaiviet', 'id')->whereNull('deleted_at')],
            'detail' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'integer', Rule::in([0, 1, 2])],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute khong duoc de trong.',
            'string' => ':attribute phai la chuoi.',
            'max' => ':attribute khong duoc vuot qua :max ky tu.',
            'integer' => ':attribute phai la so nguyen.',
            'slug.unique' => 'Slug bai viet da ton tai.',
            'slug.regex' => 'Slug chi gom chu thuong khong dau, so va dau gach ngang.',
            'topic_id.exists' => 'Chu de khong ton tai hoac da bi xoa.',
            'status.in' => 'Trang thai khong hop le.',
        ];
    }

    public function attributes(): array
    {
        return ['title' => 'Tieu de', 'detail' => 'Noi dung', 'image' => 'Hinh anh', 'description' => 'Mo ta', 'topic_id' => 'Chu de', 'status' => 'Trang thai'];
    }
}
