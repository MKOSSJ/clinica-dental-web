<?php
// app/Http/Requests/UpdateAppointmentStatusRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:pending,confirmed,completed'],
            // "cancelled" se deja fuera a propósito: para cancelar ya existe
            // el endpoint /citas/{id}/cancelar, que además valida que no esté ya cancelada.
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'El estado es obligatorio.',
            'status.in'        => 'El estado debe ser pending, confirmed o completed.',
        ];
    }
}