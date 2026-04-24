<?php

use App\Http\Controllers\MedicoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('medicos.index');
});

Route::resource('medicos', MedicoController::class)
    ->only(['index', 'create', 'store', 'edit', 'update']);