<?php
// app/Http/Requests/StoreDoctorRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el control de rol admin va en la ruta (middleware)
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'specialty' => ['nullable', 'string', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:20'],
            'email'     => ['nullable', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del doctor es obligatorio.',
            'email.email'   => 'El correo no tiene un formato válido.',
        ];
    }
}