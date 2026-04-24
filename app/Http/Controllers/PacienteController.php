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
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|in:masculino,femenino,otro',
            'ci' => ['required', 'regex:/^[0-9]+$/', 'unique:pacientes,ci'],
            'direccion' => 'required',
            'telefono' => ['required', 'regex:/^[0-9\-\s\+]+$/'],
            'email' => 'required|email',
            'grupo_sanguineo' => 'required',
            'contacto_emergencia_nombre' => 'required',
            'contacto_emergencia_telefono' => ['required', 'regex:/^[0-9\-\s\+]+$/'],
            'alergias' => 'required',
            'observaciones_generales' => 'required',
        ], [
            'nombres.required' => 'El nombre es obligatorio',
            'apellidos.required' => 'El apellido es obligatorio',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria',
            'fecha_nacimiento.date' => 'La fecha de nacimiento no es válida',
            'sexo.required' => 'El sexo es obligatorio',
            'sexo.in' => 'El sexo seleccionado no es válido',
            'ci.required' => 'El CI es obligatorio',
            'ci.unique' => 'Este CI ya está registrado',
            'ci.regex' => 'El CI debe contener solo números',
            'direccion.required' => 'La dirección es obligatoria',
            'telefono.required' => 'El teléfono es obligatorio',
            'telefono.regex' => 'El teléfono no es válido',
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'El correo no es válido',
            'grupo_sanguineo.required' => 'El grupo sanguíneo es obligatorio',
            'contacto_emergencia_nombre.required' => 'El contacto de emergencia es obligatorio',
            'contacto_emergencia_telefono.required' => 'El teléfono de emergencia es obligatorio',
            'contacto_emergencia_telefono.regex' => 'El teléfono de emergencia no es válido',
            'alergias.required' => 'Las alergias son obligatorias (si no tiene, escribe "Ninguna")',
            'observaciones_generales.required' => 'Las observaciones generales son obligatorias',
        ]);

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

    public function update(Request $request, Paciente $paciente)
    {
        $request->validate([
            'nombres' => 'required',
            'apellidos' => 'required',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|in:masculino,femenino,otro',
            'ci' => ['required', 'regex:/^[0-9]+$/', 'unique:pacientes,ci,' . $paciente->id_paciente . ',id_paciente'],
            'direccion' => 'required',
            'telefono' => ['required', 'regex:/^[0-9\-\s\+]+$/'],
            'email' => 'required|email',
            'grupo_sanguineo' => 'required',
            'contacto_emergencia_nombre' => 'required',
            'contacto_emergencia_telefono' => ['required', 'regex:/^[0-9\-\s\+]+$/'],
            'alergias' => 'required',
            'observaciones_generales' => 'required',
        ], [
            'ci.unique' => 'Este CI ya está registrado',
            'ci.regex' => 'El CI debe contener solo números',
            'telefono.regex' => 'El teléfono no es válido',
            'contacto_emergencia_telefono.regex' => 'El teléfono de emergencia no es válido',
        ]);

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