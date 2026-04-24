<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function index()
    {
        return view('historial.index');
    }

    public function show($id)
    {
        return view('historial.show');
    }
}
