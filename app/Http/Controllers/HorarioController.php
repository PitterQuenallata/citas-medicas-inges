<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index()
    {
        return view('horarios.index');
    }

    public function create()
    {
        return view('horarios.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('horarios.index');
    }

    public function edit($id)
    {
        return view('horarios.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('horarios.index');
    }
}
