<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\DoctorController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('role:admin')->get('/admin', function (Request $request) {
        return response()->json([
            'status' => true,
            'message' => 'Acceso de administrador confirmado',
            'data' => [],
        ]);
    });
});
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('pacientes', PatientController::class);
});
Route::middleware('auth:sanctum')->group(function () {
    // Consulta abierta a cualquier autenticado
    Route::get('doctores', [DoctorController::class, 'index']);
    Route::get('doctores/{doctor}', [DoctorController::class, 'show']);
    Route::get('doctores/{doctor}/horarios', [DoctorController::class, 'horarios']);

    // Solo admin puede crear/editar/eliminar
    Route::middleware('role:admin')->group(function () {
        Route::post('doctores', [DoctorController::class, 'store']);
        Route::put('doctores/{doctor}', [DoctorController::class, 'update']);
        Route::delete('doctores/{doctor}', [DoctorController::class, 'destroy']);
    });
});