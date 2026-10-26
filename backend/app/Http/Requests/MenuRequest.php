<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->roles === 'admin' && $this->user()?->status === 1;
    }

    public function rules(): array
    {
        $tables = ['category' => 'danhmuc', 'brand' => 'thuonghieu', 'topic' => 'chudebaiviet', 'page' => 'baiviet'];
        $type = $this->input('type');
        $targetRules = ['nullable', 'integer', 'min:1'];
        if (is_string($type) && isset($tables[$type])) {
            $exists = Rule::exists($tables[$type], 'id')->whereNull('deleted_at');
            if ($type === 'page') {
                $exists->where('post_type', 'page');
            }
            $targetRules = ['required', 'integer', 'min:1', $exists];
        } elseif ($type === 'custom') {
            $targetRules = ['prohibited'];
        }

        return [
            'sort_order' => ['sometimes','integer','min:0','max:9999'],
            'name' => ['required', 'string', 'max:255'],
            'link' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                $internal = preg_match('~^/(?!/)[^\\\\\s]*$~u', $value);
                $external = filter_var($value, FILTER_VALIDATE_URL)
                    && in_array(strtolower(parse_url($value, PHP_URL_SCHEME) ?? ''), ['http', 'https'], true);
                if (!$internal && !$external) {
                    $fail('Duong dan phai bat dau bang / hoac http://, https://.');
                }
            }],
            'type' => ['required', Rule::in(['custom', 'category', 'brand', 'topic', 'page'])],
            'table_id' => $targetRules,
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
            'min' => ':attribute khong hop le.',
            'in' => ':attribute khong hop le.',
            'table_id.exists' => 'Doi tuong lien ket khong ton tai hoac da bi xoa.',
            'table_id.prohibited' => 'Menu tuy chinh khong can doi tuong lien ket.',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'Ten menu', 'link' => 'Duong dan', 'type' => 'Loai menu', 'table_id' => 'Doi tuong lien ket', 'status' => 'Trang thai'];
    }
}
