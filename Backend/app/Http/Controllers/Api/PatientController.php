<?php
// app/Http/Controllers/Api/PatientController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('name')->paginate(10);

        return $this->success(
            $patients,
            'Pacientes obtenidos correctamente'
        );
    }

    public function show(Patient $patient)
    {
        return $this->success(
            $patient,
            'Paciente encontrado'
        );
    }

    public function store(StorePatientRequest $request)
    {
        $patient = Patient::create($request->validated());

        return $this->success(
            $patient,
            'Paciente registrado correctamente',
            201
        );
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $patient->update($request->validated());

        return $this->success(
            $patient,
            'Paciente actualizado correctamente'
        );
    }

    public function destroy(Patient $patient)
    {
        if ($patient->appointments()->exists()) {
            return $this->error(
                'No se puede eliminar: el paciente tiene citas registradas.',
                409
            );
        }

        $patient->delete();

        return $this->success(
            null,
            'Paciente eliminado correctamente'
        );
    }
}