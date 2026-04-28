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
use App\Http\Controllers\PagoController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Citas
    Route::middleware('permiso:acceso_citas')->group(function () {
        Route::resource('citas', CitasController::class);
        Route::patch('citas/{cita}/cancelar',    [CitasController::class, 'cancelar'])->name('citas.cancelar');
        Route::patch('citas/{cita}/confirmar',   [CitasController::class, 'confirmar'])->name('citas.confirmar');
        Route::patch('citas/{cita}/atender',     [CitasController::class, 'atender'])->name('citas.atender');
        Route::get('citas/{cita}/reprogramar',   [CitasController::class, 'showReprogramar'])->name('citas.reprogramar');
        Route::patch('citas/{cita}/reprogramar', [CitasController::class, 'storeReprogramar'])->name('citas.storeReprogramar');
        Route::get('agenda', [CitasController::class, 'agenda'])->name('agenda');
        Route::get('api/medicos/{medico}/disponibilidad',       [CitasController::class, 'disponibilidad'])->name('api.disponibilidad');
        Route::get('api/medicos/{medico}/slots',                [CitasController::class, 'slots'])->name('api.slots');
        Route::get('api/especialidades/{especialidad}/medicos', [CitasController::class, 'medicosPorEspecialidad'])->name('api.medicos.por.especialidad');
    });

    // Medicos, Especialidades, Horarios
    Route::middleware('permiso:acceso_medicos')->group(function () {
        Route::resource('medicos', MedicoController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::resource('especialidades', EspecialidadController::class)->only(['index', 'create', 'store', 'edit', 'update']);
        Route::resource('horarios', HorarioController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    });

    // Pacientes, Historial
    Route::middleware('permiso:acceso_pacientes')->group(function () {
        Route::resource('pacientes', PacienteController::class);
        Route::patch('pacientes/{paciente}/activar', [PacienteController::class, 'activar'])->name('pacientes.activar');
        Route::get('/validar-ci', [PacienteController::class, 'validarCI'])->name('validar.ci');
        Route::resource('historial', HistorialController::class)->only(['index', 'show']);
    });

    // Administracion: Usuarios, Roles, Permisos
    Route::middleware('permiso:acceso_usuarios')->group(function () {
        Route::resource('usuarios', UsuariosController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::patch('usuarios/{usuario}/activar', [UsuariosController::class, 'activar'])->name('usuarios.activar');
        Route::resource('roles', RolController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('permisos', PermisoController::class)->only(['index']);
    });

    // Pagos
    Route::middleware('permiso:acceso_citas')->group(function () {
        Route::get('pagos', [PagoController::class, 'index'])->name('pagos.index');
        Route::get('pagos/{pago}', [PagoController::class, 'show'])->name('pagos.show');
        Route::post('pagos', [PagoController::class, 'store'])->name('pagos.store');
        Route::patch('pagos/{pago}/anular', [PagoController::class, 'anular'])->name('pagos.anular');
        Route::post('api/pagos/generar-qr', [PagoController::class, 'generarQR'])->name('api.pagos.generar-qr');
        Route::get('api/pagos/verificar-qr/{movimientoId}', [PagoController::class, 'verificarQR'])->name('api.pagos.verificar-qr');
    });

    // Reportes
    Route::get('reportes', [ReportesController::class, 'index'])->name('reportes.index')->middleware('permiso:acceso_reportes');

    // Auditoria
    Route::get('auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index')->middleware('permiso:acceso_auditoria');

    // Notificaciones
    Route::get('notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index')->middleware('permiso:acceso_notificaciones');
});
