<?php

use App\Http\Controllers\MedicoController;

Route::middleware('auth')->group(function () {
    Route::resource('medicos', MedicoController::class)
         ->only(['index', 'create', 'store', 'edit', 'update']);
});