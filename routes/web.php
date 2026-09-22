<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\RecursoController;
use App\Http\Controllers\Web\ReporteController;
use App\Http\Controllers\Web\SolicitudController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
    Route::get('/solicitudes/{id}', [SolicitudController::class, 'show'])->name('solicitudes.show');
    Route::get('/recursos', [RecursoController::class, 'index'])->name('recursos.index');
    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
});
