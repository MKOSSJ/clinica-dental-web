<?php
// app/Http/Requests/RescheduleAppointmentRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_date' => ['required', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'appointment_date.required' => 'La nueva fecha y hora son obligatorias.',
            'appointment_date.after'    => 'La cita debe reagendarse a una fecha futura.',
        ];
    }
}