<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use Illuminate\Http\Request;

class MedicoController extends Controller
{
    public function index()
    {
        $medicos = Medico::query()
            ->orderBy('id_medico', 'desc')
            ->paginate(10);

        return view('medicos.index', compact('medicos'));
    }

    public function create()
    {
        return view('medicos.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_usuario' => ['required', 'integer', 'unique:medicos,id_usuario'],
            'codigo_medico' => ['required', 'string', 'max:30', 'unique:medicos,codigo_medico'],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'ci' => ['nullable', 'string', 'max:20'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'matricula_profesional' => ['required', 'string', 'max:50', 'unique:medicos,matricula_profesional'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        Medico::query()->create($data);

        return redirect()->route('medicos.index');
    }

    public function edit(Medico $medico)
    {
        return view('medicos.edit', compact('medico'));
    }

    public function update(Request $request, Medico $medico)
    {
        $data = $request->validate([
            'id_usuario' => ['required', 'integer', 'unique:medicos,id_usuario,' . $medico->id_medico . ',id_medico'],
            'codigo_medico' => ['required', 'string', 'max:30', 'unique:medicos,codigo_medico,' . $medico->id_medico . ',id_medico'],
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'ci' => ['nullable', 'string', 'max:20'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'matricula_profesional' => ['required', 'string', 'max:50', 'unique:medicos,matricula_profesional,' . $medico->id_medico . ',id_medico'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        $medico->update($data);

        return redirect()->route('medicos.index');
    }

    public function destroy(Medico $medico)
    {
        $medico->delete();

        return redirect()->route('medicos.index');
    }
}
