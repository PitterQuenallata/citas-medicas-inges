<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $pacientes = Paciente::when($request->buscar, function ($q) use ($request) {
            $q->where('nombres', 'like', "%{$request->buscar}%")
              ->orWhere('apellidos', 'like', "%{$request->buscar}%")
              ->orWhere('ci', 'like', "%{$request->buscar}%");
        })->paginate(10);

        return view('pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        // VALIDACIONES
        $request->validate([
            'nombres' => 'required',
            'apellidos' => 'required',
            'ci' => 'required|unique:pacientes,ci',
        ], [
            'nombres.required' => 'El nombre es obligatorio',
            'apellidos.required' => 'El apellido es obligatorio',
            'ci.required' => 'El CI es obligatorio',
            'ci.unique' => 'Este CI ya está registrado',
        ]);

        // 🔥 GENERAR CÓDIGO AUTOMÁTICO
        $ultimo = Paciente::latest('id_paciente')->first();

        $numero = $ultimo ? $ultimo->id_paciente + 1 : 1;

        $codigo = 'PAC-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        // GUARDAR
        Paciente::create([
            'codigo_paciente' => $codigo,
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'ci' => $request->ci,
            'telefono' => $request->telefono,
            'estado' => $request->estado ?? 'activo',
        ]);

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente creado correctamente');
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $request->validate([
            'nombres' => 'required',
            'apellidos' => 'required',
            'ci' => 'required|unique:pacientes,ci,' . $paciente->id_paciente . ',id_paciente',
        ], [
            'ci.unique' => 'Este CI ya está registrado',
        ]);

        $paciente->update($request->all());

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente actualizado');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->delete();
        return back();
    }

    public function validarCI(Request $request)
{
    $existe = \App\Models\Paciente::where('ci', $request->ci)->exists();

    return response()->json([
        'existe' => $existe
    ]);
}
}