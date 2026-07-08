<?php
// app/Http/Controllers/Api/DoctorController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::query();

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('specialty', 'like', "%{$search}%");
        }

        $doctors = $query->orderBy('name')->paginate(10);

        return response()->json([
            'status'  => true,
            'message' => 'Doctores obtenidos correctamente',
            'data'    => $doctors,
        ]);
    }

    public function show(Doctor $doctor)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Doctor encontrado',
            'data'    => $doctor,
        ]);
    }

    public function store(StoreDoctorRequest $request)
    {
        $doctor = Doctor::create($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Doctor registrado correctamente',
            'data'    => $doctor,
        ], 201);
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $doctor->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Doctor actualizado correctamente',
            'data'    => $doctor,
        ]);
    }

    public function destroy(Doctor $doctor)
    {
        if ($doctor->appointments()->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'No se puede eliminar: el doctor tiene citas registradas.',
                'data'    => null,
            ], 409);
        }

        $doctor->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Doctor eliminado correctamente',
            'data'    => null,
        ]);
    }

    public function horarios(Request $request, Doctor $doctor)
    {
        $request->validate([
            'fecha' => ['required', 'date_format:Y-m-d'],
        ]);

        $fecha = $request->query('fecha');

        $startHour = config('clinic.work_start_hour');
        $endHour   = config('clinic.work_end_hour');
        $slotMin   = config('clinic.slot_minutes');

        // Generar todos los slots posibles del día
        $slots = [];
        $current = Carbon::parse($fecha)->setTime($startHour, 0);
        $end = Carbon::parse($fecha)->setTime($endHour, 0);

        while ($current < $end) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($slotMin);
        }

        // Horas ya ocupadas por citas activas (no canceladas) de ese doctor en esa fecha
        $ocupadas = $doctor->appointments()
            ->whereDate('appointment_date', $fecha)
            ->where('status', '!=', 'cancelled')
            ->pluck('appointment_date')
            ->map(fn ($dt) => Carbon::parse($dt)->format('H:i'))
            ->toArray();

        $disponibles = array_values(array_diff($slots, $ocupadas));

        return response()->json([
            'status'  => true,
            'message' => 'Horarios obtenidos correctamente',
            'data'    => [
                'fecha'       => $fecha,
                'disponibles' => $disponibles,
                'ocupados'    => array_values($ocupadas),
            ],
        ]);
    }
}