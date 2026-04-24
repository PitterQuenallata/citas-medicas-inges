<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PermisoController extends Controller
{
    public function index()   { return view('permisos.index'); }
    public function create()  { return view('permisos.create'); }
    public function store(Request $r)  { return redirect()->route('permisos.index'); }
    public function edit($id) { return view('permisos.edit'); }
    public function update(Request $r, $id) { return redirect()->route('permisos.index'); }
}
