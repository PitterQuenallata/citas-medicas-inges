<?php

use App\Http\Controllers\Api\MedicoHorarioController;
use Illuminate\Support\Facades\Route;

Route::get('medicos/{medico}/horarios', MedicoHorarioController::class)
    ->name('api.medicos.horarios');