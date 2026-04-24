<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function index()   { return view('reportes.index'); }
    public function create()  { return view('reportes.create'); }
    public function store(Request $r)  { return redirect()->route('reportes.index'); }
    public function edit($id) { return view('reportes.edit'); }
    public function update(Request $r, $id) { return redirect()->route('reportes.index'); }
}
