<?php

use App\Http\Controllers\CitasController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('citas.index');
});

Route::resource('citas', CitasController::class);

Route::patch('citas/{cita}/cancelar',       [CitasController::class, 'cancelar'])        ->name('citas.cancelar');
Route::get('citas/{cita}/reprogramar',      [CitasController::class, 'showReprogramar']) ->name('citas.reprogramar');
Route::patch('citas/{cita}/reprogramar',    [CitasController::class, 'storeReprogramar'])->name('citas.storeReprogramar');

Route::get('agenda', [CitasController::class, 'agenda'])->name('agenda');

Route::get('api/medicos/{medico}/disponibilidad',          [CitasController::class, 'disponibilidad'])        ->name('api.disponibilidad');
Route::get('api/medicos/{medico}/slots',                   [CitasController::class, 'slots'])                 ->name('api.slots');
Route::get('api/especialidades/{especialidad}/medicos',    [CitasController::class, 'medicosPorEspecialidad'])->name('api.medicos.por.especialidad');
