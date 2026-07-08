<?php
// app/Http/Requests/CitasPorDiaRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CitasPorDiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha' => ['required', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha.required'    => 'La fecha es obligatoria.',
            'fecha.date_format' => 'La fecha debe tener el formato YYYY-MM-DD.',
        ];
    }
}