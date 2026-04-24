<?php

use App\Http\Controllers\MedicoController;
use App\Http\Controllers\CitasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EspecialidadController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\HistorialController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('medicos', MedicoController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::resource('pacientes', PacienteController::class);
    Route::get('/validar-ci', [PacienteController::class, 'validarCI'])->name('validar.ci');

    Route::resource('citas', CitasController::class);
    Route::patch('citas/{cita}/cancelar',    [CitasController::class, 'cancelar'])->name('citas.cancelar');
    Route::get('citas/{cita}/reprogramar',   [CitasController::class, 'showReprogramar'])->name('citas.reprogramar');
    Route::patch('citas/{cita}/reprogramar', [CitasController::class, 'storeReprogramar'])->name('citas.storeReprogramar');

    Route::get('agenda', [CitasController::class, 'agenda'])->name('agenda');

    Route::resource('especialidades', EspecialidadController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('horarios', HorarioController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('historial', HistorialController::class)->only(['index', 'show']);

    Route::get('api/medicos/{medico}/disponibilidad',       [CitasController::class, 'disponibilidad'])->name('api.disponibilidad');
    Route::get('api/medicos/{medico}/slots',                [CitasController::class, 'slots'])->name('api.slots');
    Route::get('api/especialidades/{especialidad}/medicos', [CitasController::class, 'medicosPorEspecialidad'])->name('api.medicos.por.especialidad');
});
