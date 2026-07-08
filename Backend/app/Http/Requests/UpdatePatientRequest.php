<?php
// app/Http/Requests/UpdatePatientRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => ['sometimes', 'required', 'string', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'email'      => ['nullable', 'email', 'max:255'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'address'    => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'El nombre es obligatorio.',
            'email.email'               => 'El correo no tiene un formato válido.',
            'birth_date.before_or_equal'=> 'La fecha de nacimiento no puede ser futura.',
        ];
    }
}