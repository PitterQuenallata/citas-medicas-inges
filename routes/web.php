<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PacienteController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('pacientes', PacienteController::class);
Route::get('/validar-ci', [PacienteController::class, 'validarCI'])->name('validar.ci');