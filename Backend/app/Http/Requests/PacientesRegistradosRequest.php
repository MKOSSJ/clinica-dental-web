<?php
// app/Http/Requests/PacientesRegistradosRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PacientesRegistradosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'desde' => ['required', 'date_format:Y-m-d'],
            'hasta' => ['required', 'date_format:Y-m-d', 'after_or_equal:desde'],
        ];
    }

    public function messages(): array
    {
        return [
            'desde.required'       => 'La fecha inicial es obligatoria.',
            'hasta.required'       => 'La fecha final es obligatoria.',
            'hasta.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
        ];
    }
}