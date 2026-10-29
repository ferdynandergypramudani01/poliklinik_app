<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'current_password',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'different:current_password',
            ],

            'password_confirmation' => [
                'required',
                'string',
                'same:password',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Password lama wajib diisi.',

            'current_password.current_password' => 'Password lama tidak sesuai dengan password akun Anda.',

            'password.required' => 'Password baru wajib diisi.',

            'password.min' => 'Password baru minimal 8 karakter.',

            'password.different' => 'Password baru tidak boleh sama dengan password lama.',

            'password_confirmation.required' => 'Konfirmasi password baru wajib diisi.',

            'password_confirmation.same' => 'Konfirmasi password baru tidak cocok.',
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => 'password lama',
            'password' => 'password baru',
            'password_confirmation' => 'konfirmasi password baru',
        ];
    }
}
