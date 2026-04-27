<?php

namespace App\Http\Controllers;

use App\Models\Permiso;

class PermisoController extends Controller
{
    public function index()
    {
        $permisos = Permiso::with('roles')->orderBy('modulo')->get();
        return view('permisos.index', compact('permisos'));
    }
}
