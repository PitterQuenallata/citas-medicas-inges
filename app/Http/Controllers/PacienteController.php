<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use App\Http\Requests\StorePacienteRequest;
use App\Http\Requests\UpdatePacienteRequest;

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

    public function store(StorePacienteRequest $request)
    {
        $paciente = Paciente::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'sexo' => $request->sexo,
            'ci' => $request->ci,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'grupo_sanguineo' => $request->grupo_sanguineo,
            'contacto_emergencia_nombre' => $request->contacto_emergencia_nombre,
            'contacto_emergencia_telefono' => $request->contacto_emergencia_telefono,
            'alergias' => $request->alergias,
            'observaciones_generales' => $request->observaciones_generales,
            'estado' => $request->estado ?? 'activo',
        ]);

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente creado correctamente')
            ->with('swal_success', 'Paciente creado correctamente');
    }

    public function show(Paciente $paciente)
    {
        $paciente->load('citas.medico');
        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(UpdatePacienteRequest $request, Paciente $paciente)
    {
        $paciente->update($request->only([
            'nombres',
            'apellidos',
            'fecha_nacimiento',
            'sexo',
            'ci',
            'direccion',
            'telefono',
            'email',
            'grupo_sanguineo',
            'contacto_emergencia_nombre',
            'contacto_emergencia_telefono',
            'alergias',
            'observaciones_generales',
            'estado',
        ]));

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente actualizado');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->update([
            'estado' => 'inactivo',
        ]);
        return back()->with('success', 'Paciente desactivado correctamente');
    }

    public function activar(Paciente $paciente)
    {
        $paciente->update([
            'estado' => 'activo',
        ]);

        return back()->with('success', 'Paciente activado correctamente');
    }

    public function validarCI(Request $request)
    {
        $request->validate([
            'ci' => ['required', 'regex:/^[0-9]+$/'],
        ], [
            'ci.regex' => 'El CI debe contener solo números',
        ]);

        $existe = Paciente::where('ci', $request->ci)->exists();

        return response()->json([
            'existe' => $existe
        ]);
    }
}