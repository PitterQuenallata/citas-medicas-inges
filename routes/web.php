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
    Route::get('/dashboard/analytics', [DashboardController::class, 'analytics'])->name('dashboard.analytics');
    Route::get('/dashboard/agenda', [DashboardController::class, 'agenda'])->name('dashboard.agenda');
    Route::get('/dashboard/calendario', [DashboardController::class, 'calendario'])->name('dashboard.calendario');

    // API calendario (accesible desde Dashboard y Citas)
    Route::get('api/citas/eventos', [CitasController::class, 'calendarEvents'])->name('api.citas.eventos');

    // Citas
    Route::middleware('permiso:acceso_citas')->group(function () {
        Route::get('citas/calendario', [CitasController::class, 'calendario'])->name('citas.calendario');
        Route::resource('citas', CitasController::class);
        Route::patch('citas/{cita}/cancelar',    [CitasController::class, 'cancelar'])->name('citas.cancelar');
        Route::patch('citas/{cita}/confirmar',   [CitasController::class, 'confirmar'])->name('citas.confirmar');
        Route::patch('citas/{cita}/atender',     [CitasController::class, 'atender'])->name('citas.atender');
        Route::patch('citas/{cita}/no-asistio', [CitasController::class, 'noAsistio'])->name('citas.noAsistio');
        Route::get('citas/{cita}/reprogramar',   [CitasController::class, 'showReprogramar'])->name('citas.reprogramar');
        Route::patch('citas/{cita}/reprogramar', [CitasController::class, 'storeReprogramar'])->name('citas.storeReprogramar');
        Route::get('citas/{cita}/ticket', [CitasController::class, 'ticket'])->name('citas.ticket');
        Route::get('agenda', [CitasController::class, 'agenda'])->name('agenda');
        Route::get('api/medicos/{medico}/disponibilidad',       [CitasController::class, 'disponibilidad'])->name('api.disponibilidad');
        Route::get('api/medicos/{medico}/slots',                [CitasController::class, 'slots'])->name('api.slots');
        Route::get('api/especialidades/{especialidad}/medicos', [CitasController::class, 'medicosPorEspecialidad'])->name('api.medicos.por.especialidad');
    });

    // Medicos, Especialidades, Horarios
    Route::middleware('permiso:acceso_medicos')->group(function () {
        Route::resource('medicos', MedicoController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::patch('medicos/{medico}/desactivar', [MedicoController::class, 'desactivar'])->name('medicos.desactivar');
        Route::patch('medicos/{medico}/activar', [MedicoController::class, 'activar'])->name('medicos.activar');
        // NUEVO: vista de horarios agrupados por médico
        Route::get('medicos/{medico}/horarios', [MedicoController::class, 'horarios'])->name('medicos.horarios');
        Route::resource('especialidades', EspecialidadController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->parameters(['especialidades' => 'especialidad']); // NUEVO: fuerza {especialidad} para que el binding coincida con $especialidad en el controlador
        Route::resource('horarios', HorarioController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    });

    // Pacientes, Historial
    Route::middleware('permiso:acceso_pacientes')->group(function () {
        Route::resource('pacientes', PacienteController::class);
        Route::patch('pacientes/{paciente}/activar', [PacienteController::class, 'activar'])->name('pacientes.activar');
        Route::get('/validar-ci', [PacienteController::class, 'validarCI'])->name('validar.ci');
        Route::get('historial', [HistorialController::class, 'index'])->name('historial.index');
        Route::get('historial/{paciente}', [HistorialController::class, 'show'])->name('historial.show');
        Route::get('historial/{paciente}/consultas/create', [HistorialController::class, 'create'])->name('historial.consultas.create');
        Route::post('historial/{paciente}/consultas', [HistorialController::class, 'store'])->name('historial.consultas.store');
        Route::get('historial/consultas/{consulta}/edit', [HistorialController::class, 'edit'])->name('historial.consultas.edit');
        Route::put('historial/consultas/{consulta}', [HistorialController::class, 'update'])->name('historial.consultas.update');
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
        Route::get('pagos/{pago}/recibo', [PagoController::class, 'recibo'])->name('pagos.recibo');
        Route::post('api/pagos/generar-qr', [PagoController::class, 'generarQR'])->name('api.pagos.generar-qr');
        Route::get('api/pagos/verificar-qr/{movimientoId}', [PagoController::class, 'verificarQR'])->name('api.pagos.verificar-qr');
    });

    // Reportes
    Route::middleware('permiso:acceso_reportes')->prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/',                  [ReportesController::class, 'index'])->name('index');
        Route::get('/citas',             [ReportesController::class, 'citas'])->name('citas');
        Route::get('/medicos',           [ReportesController::class, 'medicos'])->name('medicos');
        Route::get('/pacientes',         [ReportesController::class, 'pacientes'])->name('pacientes');
        Route::get('/pagos',             [ReportesController::class, 'pagos'])->name('pagos');
        Route::get('/especialidades',    [ReportesController::class, 'especialidades'])->name('especialidades');
        Route::get('/notificaciones',    [ReportesController::class, 'notificaciones'])->name('notificaciones');
        Route::get('/canceladas',        [ReportesController::class, 'canceladas'])->name('canceladas');
        Route::get('/resumen-mensual',   [ReportesController::class, 'resumenMensual'])->name('resumen-mensual');
        Route::get('/pdf/{tipo}',        [ReportesController::class, 'exportarPdf'])->name('pdf');
    });

    // Auditoria
    Route::middleware('permiso:acceso_auditoria')->prefix('auditoria')->name('auditoria.')->group(function () {
        Route::get('/',    [AuditoriaController::class, 'index'])->name('index');
        Route::get('/pdf', [AuditoriaController::class, 'exportarPdf'])->name('pdf');
    });

    // Notificaciones
    Route::middleware('permiso:acceso_notificaciones')->group(function () {
        Route::get('notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
        Route::post('notificaciones/enviar/{cita}', [NotificacionController::class, 'enviar'])->name('notificaciones.enviar');
        Route::post('notificaciones/enviar-hoy', [NotificacionController::class, 'enviarHoy'])->name('notificaciones.enviar-hoy');
    });
});
