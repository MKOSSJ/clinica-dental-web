<?php
// app/Http/Controllers/Api/NotificationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function notificar(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor']);

        Log::info('Notificación WhatsApp enviada', [
            'appointment_id' => $appointment->id,
            'paciente'       => $appointment->patient?->name,
            'doctor'         => $appointment->doctor?->name,
            'fecha'          => $appointment->appointment_date,
            'enviado_por'    => request()->user()->name,
            'enviado_en'     => now(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Notificación registrada correctamente',
            'data'    => null,
        ]);
    }
}
