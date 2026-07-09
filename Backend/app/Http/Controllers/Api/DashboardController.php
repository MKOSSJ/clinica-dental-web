<?php
// app/Http/Controllers/Api/DashboardController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $hoy = Carbon::today();

        $totalPacientes = Patient::count();
        $totalDoctores = Doctor::count();
        $citasHoy = Appointment::whereDate('appointment_date', $hoy)->count();
        $citasPendientes = Appointment::where('status', 'pending')->count();

        // Citas de los últimos 7 días (incluyendo hoy), agrupadas por día
        $desde = $hoy->copy()->subDays(6);

        $citasPorDia = Appointment::whereDate('appointment_date', '>=', $desde)
            ->whereDate('appointment_date', '<=', $hoy)
            ->selectRaw('DATE(appointment_date) as fecha, count(*) as total')
            ->groupBy('fecha')
            ->pluck('total', 'fecha');

        $ultimos7Dias = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = $hoy->copy()->subDays($i)->format('Y-m-d');
            $ultimos7Dias[] = [
                'fecha' => $fecha,
                'total' => $citasPorDia[$fecha] ?? 0,
            ];
        }

        return response()->json([
            'status'  => true,
            'message' => 'Resumen del dashboard obtenido correctamente',
            'data'    => [
                'totalPacientes'   => $totalPacientes,
                'totalDoctores'    => $totalDoctores,
                'citasHoy'         => $citasHoy,
                'citasPendientes'  => $citasPendientes,
                'citasUltimos7Dias' => $ultimos7Dias,
            ],
        ]);
    }
}