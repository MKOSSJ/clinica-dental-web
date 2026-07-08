<?php
// app/Http/Requests/StoreAppointmentRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id'       => ['required', 'integer', 'exists:patients,id'],
            'doctor_id'        => ['required', 'integer', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
            'notes'            => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required'       => 'Debes seleccionar un paciente.',
            'patient_id.exists'         => 'El paciente seleccionado no existe.',
            'doctor_id.required'        => 'Debes seleccionar un doctor.',
            'doctor_id.exists'          => 'El doctor seleccionado no existe.',
            'appointment_date.required' => 'La fecha y hora son obligatorias.',
            'appointment_date.after'    => 'La cita debe ser en una fecha futura.',
        ];
    }
}