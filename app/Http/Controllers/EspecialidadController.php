<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function index()
    {
        return view('especialidades.index');
    }

    public function create()
    {
        return view('especialidades.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('especialidades.index');
    }

    public function edit($id)
    {
        return view('especialidades.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('especialidades.index');
    }
}
