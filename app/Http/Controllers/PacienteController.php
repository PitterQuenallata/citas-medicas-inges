<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::query()
            ->orderBy('id_paciente', 'desc')
            ->paginate(10);

        return view('pacientes.index', compact('pacientes'));
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date'],
            'sexo' => ['required', 'in:masculino,femenino,otro'],
            'ci' => ['required', 'numeric', 'digits_between:6,20', 'unique:pacientes,ci'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'numeric', 'digits_between:7,20'],
            'email' => ['required', 'email', 'max:150'],
            'grupo_sanguineo' => ['nullable', 'string', 'max:10'],
            'contacto_emergencia_nombre' => ['nullable', 'string', 'max:150'],
            'contacto_emergencia_telefono' => ['nullable', 'numeric', 'digits_between:7,20'],
            'alergias' => ['nullable', 'string'],
            'observaciones_generales' => ['nullable', 'string'],
            'estado' => ['required', 'in:activo,inactivo'],
        ], [
            'ci.numeric' => 'Ingrese un numero valido.',
            'ci.unique' => 'CI duplicado.',
            'telefono.numeric' => 'Ingrese un numero valido.',
            'contacto_emergencia_telefono.numeric' => 'Ingrese un numero valido.',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $lastCodigo = DB::table('pacientes')
                    ->select('codigo_paciente')
                    ->orderByDesc('id_paciente')
                    ->lockForUpdate()
                    ->value('codigo_paciente');

                $next = 1;
                if (is_string($lastCodigo) && preg_match('/PAC-(\d+)/', $lastCodigo, $m)) {
                    $next = ((int) $m[1]) + 1;
                }

                $codigo = 'PAC-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);

                Paciente::query()->create([
                    ...$data,
                    'codigo_paciente' => $codigo,
                ]);
            });

            return redirect()->route('pacientes.index')->with('success', 'Paciente creado correctamente.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'No se pudo crear el paciente.');
        }
    }

    public function edit(Paciente $paciente)
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente)
    {
        $data = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date'],
            'sexo' => ['required', 'in:masculino,femenino,otro'],
            'ci' => ['required', 'numeric', 'digits_between:6,20', 'unique:pacientes,ci,' . $paciente->id_paciente . ',id_paciente'],
            'direccion' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'numeric', 'digits_between:7,20'],
            'email' => ['required', 'email', 'max:150'],
            'grupo_sanguineo' => ['nullable', 'string', 'max:10'],
            'contacto_emergencia_nombre' => ['nullable', 'string', 'max:150'],
            'contacto_emergencia_telefono' => ['nullable', 'numeric', 'digits_between:7,20'],
            'alergias' => ['nullable', 'string'],
            'observaciones_generales' => ['nullable', 'string'],
            'estado' => ['required', 'in:activo,inactivo'],
        ], [
            'ci.numeric' => 'Ingrese un numero valido.',
            'ci.unique' => 'CI duplicado.',
            'telefono.numeric' => 'Ingrese un numero valido.',
            'contacto_emergencia_telefono.numeric' => 'Ingrese un numero valido.',
        ]);

        try {
            $paciente->update($data);

            return redirect()->route('pacientes.index')->with('success', 'Paciente actualizado correctamente.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'No se pudo actualizar el paciente.');
        }
    }

    public function destroy(Paciente $paciente)
    {
        try {
            $paciente->estado = 'inactivo';
            $paciente->save();

            return redirect()->route('pacientes.index')->with('success', 'Paciente inactivado correctamente.');
        } catch (\Throwable $e) {
            return redirect()->route('pacientes.index')->with('error', 'No se pudo inactivar el paciente.');
        }
    }
}
