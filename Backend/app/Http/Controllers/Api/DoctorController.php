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

        return $this->success(
            $doctors,
            'Doctores obtenidos correctamente'
        );
    }

    public function show(Doctor $doctor)
    {
        return $this->success(
            $doctor,
            'Doctor encontrado'
        );
    }

    public function store(StoreDoctorRequest $request)
    {
        $doctor = Doctor::create($request->validated());

        return $this->success(
            $doctor,
            'Doctor registrado correctamente',
            201
        );
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $doctor->update($request->validated());

        return $this->success(
            $doctor,
            'Doctor actualizado correctamente'
        );
    }

    public function destroy(Doctor $doctor)
    {
        if ($doctor->appointments()->exists()) {
            return $this->error(
                'No se puede eliminar: el doctor tiene citas registradas.',
                409
            );
        }

        $doctor->delete();

        return $this->success(
            null,
            'Doctor eliminado correctamente'
        );
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

        return $this->success(
            [
                'fecha'       => $fecha,
                'disponibles' => $disponibles,
                'ocupados'    => array_values($ocupadas),
            ],
            'Horarios obtenidos correctamente'
        );
    }
}