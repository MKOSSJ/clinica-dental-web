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

        return response()->json([
            'status'  => true,
            'message' => 'Pacientes obtenidos correctamente',
            'data'    => $patients,
        ]);
    }

    public function show(Patient $patient)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Paciente encontrado',
            'data'    => $patient,
        ]);
    }

    public function store(StorePatientRequest $request)
    {
        $patient = Patient::create($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Paciente registrado correctamente',
            'data'    => $patient,
        ], 201);
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $patient->update($request->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Paciente actualizado correctamente',
            'data'    => $patient,
        ]);
    }

    public function destroy(Patient $patient)
    {
        if ($patient->appointments()->exists()) {
            return response()->json([
                'status'  => false,
                'message' => 'No se puede eliminar: el paciente tiene citas registradas.',
                'data'    => null,
            ], 409);
        }

        $patient->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Paciente eliminado correctamente',
            'data'    => null,
        ]);
    }
}