<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index()   { return view('roles.index'); }
    public function create()  { return view('roles.create'); }
    public function store(Request $r)  { return redirect()->route('roles.index'); }
    public function edit($id) { return view('roles.edit'); }
    public function update(Request $r, $id) { return redirect()->route('roles.index'); }
}
