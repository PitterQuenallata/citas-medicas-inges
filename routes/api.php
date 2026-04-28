<?php

use App\Http\Controllers\Api\MedicoHorarioController;
use App\Http\Controllers\PagoController;
use Illuminate\Support\Facades\Route;

Route::get('medicos/{medico}/horarios', MedicoHorarioController::class)
    ->name('api.medicos.horarios');

Route::post('pagos/webhook', [PagoController::class, 'webhook'])
    ->name('api.pagos.webhook');