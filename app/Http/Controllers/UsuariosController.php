<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    public function index()   { return view('usuarios.index'); }
    public function create()  { return view('usuarios.create'); }
    public function store(Request $r)  { return redirect()->route('usuarios.index'); }
    public function edit($id) { return view('usuarios.edit'); }
    public function update(Request $r, $id) { return redirect()->route('usuarios.index'); }
}
