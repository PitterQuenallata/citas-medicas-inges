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
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\NotificacionController;

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
    Route::patch('pacientes/{paciente}/activar', [PacienteController::class, 'activar'])->name('pacientes.activar');
    Route::get('/validar-ci', [PacienteController::class, 'validarCI'])->name('validar.ci');

    Route::resource('citas', CitasController::class);
    Route::patch('citas/{cita}/cancelar',    [CitasController::class, 'cancelar'])->name('citas.cancelar');
    Route::get('citas/{cita}/reprogramar',   [CitasController::class, 'showReprogramar'])->name('citas.reprogramar');
    Route::patch('citas/{cita}/reprogramar', [CitasController::class, 'storeReprogramar'])->name('citas.storeReprogramar');

    Route::get('agenda', [CitasController::class, 'agenda'])->name('agenda');

    Route::resource('especialidades', EspecialidadController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('horarios', HorarioController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::get('historial', [HistorialController::class, 'index'])->name('historial.index');
    Route::get('historial/{paciente}', [HistorialController::class, 'show'])->name('historial.show');
    Route::get('historial/{paciente}/consultas/create', [HistorialController::class, 'create'])->name('historial.consultas.create');
    Route::post('historial/{paciente}/consultas', [HistorialController::class, 'store'])->name('historial.consultas.store');
    Route::get('historial/consultas/{consulta}/edit', [HistorialController::class, 'edit'])->name('historial.consultas.edit');
    Route::put('historial/consultas/{consulta}', [HistorialController::class, 'update'])->name('historial.consultas.update');

    Route::resource('usuarios', UsuariosController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('roles', RolController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('permisos', PermisoController::class)->only(['index']);
    Route::get('auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
    Route::get('reportes', [ReportesController::class, 'index'])->name('reportes.index');
    Route::get('notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');

    Route::get('api/medicos/{medico}/disponibilidad',       [CitasController::class, 'disponibilidad'])->name('api.disponibilidad');
    Route::get('api/medicos/{medico}/slots',                [CitasController::class, 'slots'])->name('api.slots');
    Route::get('api/especialidades/{especialidad}/medicos', [CitasController::class, 'medicosPorEspecialidad'])->name('api.medicos.por.especialidad');
});
