<?php
use App\Http\Controllers\Api\MedicoHorarioController;

Route::get('medicos/{medico}/horarios', MedicoHorarioController::class)
     ->name('api.medicos.horarios');
     