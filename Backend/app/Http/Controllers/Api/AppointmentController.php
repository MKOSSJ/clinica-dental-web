<?php
// app/Http/Controllers/Api/AppointmentController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RescheduleAppointmentRequest;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['patient', 'doctor']);

        if ($doctorId = $request->query('doctor_id')) {
            $query->where('doctor_id', $doctorId);
        }

        if ($patientId = $request->query('patient_id')) {
            $query->where('patient_id', $patientId);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($desde = $request->query('desde')) {
            $query->whereDate('appointment_date', '>=', $desde);
        }

        if ($hasta = $request->query('hasta')) {
            $query->whereDate('appointment_date', '<=', $hasta);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->paginate(10);

        return $this->success(
            $appointments,
            'Citas obtenidas correctamente'
        );
    }

    public function store(StoreAppointmentRequest $request)
    {
        $validated = $request->validated();

        if ($this->horarioOcupado($validated['doctor_id'], $validated['appointment_date'])) {
            return $this->error(
                'El doctor ya tiene una cita agendada en ese horario.',
                409
            );
        }

        $appointment = Appointment::create($validated);
        $appointment->load(['patient', 'doctor']);

        return $this->success(
            $appointment,
            'Cita registrada correctamente',
            201
        );
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'doctor']);

        return $this->success(
            $appointment,
            'Cita encontrada'
        );
    }

    public function cancelar(Appointment $appointment)
    {
        if ($appointment->status === 'cancelled') {
            return $this->error(
                'Esta cita ya está cancelada.',
                409
            );
        }

        $appointment->update([
            'status' => 'cancelled'
        ]);

        return $this->success(
            $appointment,
            'Cita cancelada correctamente'
        );
    }

    public function reagendar(RescheduleAppointmentRequest $request, Appointment $appointment)
    {
        $nuevaFecha = $request->validated()['appointment_date'];

        if ($this->horarioOcupado($appointment->doctor_id, $nuevaFecha, $appointment->id)) {
            return $this->error(
                'El doctor ya tiene una cita agendada en ese nuevo horario.',
                409
            );
        }

        $appointment->update([
            'appointment_date' => $nuevaFecha,
            'status' => 'pending',
        ]);

        return $this->success(
            $appointment,
            'Cita reagendada correctamente'
        );
    }

    public function historial($patientId)
    {
        $appointments = Appointment::with('doctor')
            ->where('patient_id', $patientId)
            ->orderBy('appointment_date', 'desc')
            ->get();

        return $this->success(
            $appointments,
            'Historial obtenido correctamente'
        );
    }

    /**
     * Verifica si el doctor ya tiene una cita activa (no cancelada) en esa fecha/hora exacta.
     * $excludeId se usa al reagendar, para no chocar contra la misma cita que se está moviendo.
     */
    private function horarioOcupado(int $doctorId, string $fecha, ?int $excludeId = null): bool
    {
        $fechaCarbon = Carbon::parse($fecha);

        $query = Appointment::where('doctor_id', $doctorId)
            ->where('appointment_date', $fechaCarbon)
            ->where('status', '!=', 'cancelled');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
    public function actualizarEstado(UpdateAppointmentStatusRequest $request, Appointment $appointment)
    {
        if ($appointment->status === 'cancelled') {
            return $this->error('No se puede cambiar el estado de una cita cancelada.', 409);
        }

        $appointment->update(['status' => $request->validated()['status']]);
        $appointment->load(['patient', 'doctor']);

        return $this->success($appointment, 'Estado de la cita actualizado correctamente');
    }
}