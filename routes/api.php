<?php

use App\Http\Controllers\Api\AdministracionController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComentarioController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EvidenciaController;
use App\Http\Controllers\Api\RecursoController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\SolicitudController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::apiResource('solicitudes', SolicitudController::class)->parameters(['solicitudes' => 'solicitud']);
    Route::post('solicitudes/{solicitud}/evidencias', [EvidenciaController::class, 'store']);
    Route::delete('evidencias/{evidencia}', [EvidenciaController::class, 'destroy']);
    Route::get('solicitudes/{solicitud}/historial', [SolicitudController::class, 'historial']);
    Route::get('solicitudes/{solicitud}/comentarios', [ComentarioController::class, 'index']);

    Route::middleware('admin')->group(function () {
        Route::post('solicitudes/{solicitud}/comentarios', [ComentarioController::class, 'store']);
        Route::patch('solicitudes/{solicitud}/estado', [AdministracionController::class, 'estado']);
        Route::patch('solicitudes/{solicitud}/prioridad', [AdministracionController::class, 'prioridad']);
        Route::patch('solicitudes/{solicitud}/responsable', [AdministracionController::class, 'responsable']);
        Route::apiResource('recursos', RecursoController::class);
        Route::get('dashboard', DashboardController::class);
        Route::get('reportes/solicitudes', [ReporteController::class, 'solicitudes']);
        Route::get('administrativos', [AdministracionController::class, 'administrativos']);
    });
});
