<?php
// app/Http/Controllers/Api/ReportController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CitasPorDiaRequest;
use App\Http\Requests\CitasPorDoctorRequest;
use App\Http\Requests\PacientesRegistradosRequest;
use App\Models\Appointment;
use App\Models\Patient;

class ReportController extends Controller
{
    public function citasPorDia(CitasPorDiaRequest $request)
    {
        $fecha = $request->validated()['fecha'];

        $conteo = Appointment::whereDate('appointment_date', $fecha)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return $this->success(
            [
                'fecha' => $fecha,
                'total' => $conteo->sum(),
                'porEstado' => [
                    'pending'   => $conteo['pending'] ?? 0,
                    'confirmed' => $conteo['confirmed'] ?? 0,
                    'cancelled' => $conteo['cancelled'] ?? 0,
                    'completed' => $conteo['completed'] ?? 0,
                ],
            ],
            'Reporte de citas por día generado correctamente'
        );
    }

    public function citasPorDoctor(CitasPorDoctorRequest $request)
    {
        $validated = $request->validated();

        $citas = Appointment::with('patient')
            ->where('doctor_id', $validated['doctor_id'])
            ->whereDate('appointment_date', '>=', $validated['desde'])
            ->whereDate('appointment_date', '<=', $validated['hasta'])
            ->orderBy('appointment_date')
            ->get();

        return $this->success(
            [
                'doctor_id' => $validated['doctor_id'],
                'desde'     => $validated['desde'],
                'hasta'     => $validated['hasta'],
                'total'     => $citas->count(),
                'citas'     => $citas,
            ],
            'Reporte de citas por doctor generado correctamente'
        );
    }

    public function pacientesRegistrados(PacientesRegistradosRequest $request)
    {
        $validated = $request->validated();

        $pacientes = Patient::whereDate('created_at', '>=', $validated['desde'])
            ->whereDate('created_at', '<=', $validated['hasta'])
            ->orderBy('created_at')
            ->get();

        return $this->success(
            [
                'desde'     => $validated['desde'],
                'hasta'     => $validated['hasta'],
                'total'     => $pacientes->count(),
                'pacientes' => $pacientes,
            ],
            'Reporte de pacientes registrados generado correctamente'
        );
    }
}