<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicoController;

Route::resource('medicos', MedicoController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);