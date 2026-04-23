<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;

        $pacientes = Paciente::when($buscar, function ($q) use ($buscar) {
            $q->where(function ($query) use ($buscar) {
                $query->where('nombres', 'like', "%$buscar%")
                    ->orWhere('apellidos', 'like', "%$buscar%")
                    ->orWhere('ci', 'like', "%$buscar%");
            });
        })
        ->orderBy('id_paciente', 'desc')
        ->paginate(10)
        ->withQueryString(); // 🔥 mantiene búsqueda en paginación

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

            $paciente = Paciente::create([
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

       $paciente->update($request->only([
    'nombres',
    'apellidos',
    'ci',
    'telefono',
    'estado'
]));

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente actualizado');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->update([
            'estado' => 'inactivo',
        ]);
        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente desactivado correctamente');
    }

        public function validarCI(Request $request)
        {
            $request->validate([
                'ci' => 'required'
            ]);

            $existe = Paciente::where('ci', $request->ci)->exists();

            return response()->json([
                'existe' => $existe
            ]);
        }
}