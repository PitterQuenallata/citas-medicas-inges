<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditoriaController extends Controller
{
    public function index()   { return view('auditoria.index'); }
    public function create()  { return view('auditoria.create'); }
    public function store(Request $r)  { return redirect()->route('auditoria.index'); }
    public function edit($id) { return view('auditoria.edit'); }
    public function update(Request $r, $id) { return redirect()->route('auditoria.index'); }
}
