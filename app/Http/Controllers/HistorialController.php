<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\ConsultaMedica;
use App\Models\Paciente;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;

        $pacientes = Paciente::query()
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($query) use ($buscar) {
                    $query->where('nombres', 'like', "%{$buscar}%")
                        ->orWhere('apellidos', 'like', "%{$buscar}%")
                        ->orWhere('ci', 'like', "%{$buscar}%")
                        ->orWhere('codigo_paciente', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('id_paciente', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('historial.index', compact('pacientes', 'buscar'));
    }

    public function show(Paciente $paciente)
    {
        $consultas = ConsultaMedica::with(['medico', 'cita'])
            ->where('id_paciente', $paciente->id_paciente)
            ->orderBy('fecha_consulta', 'desc')
            ->paginate(10)
            ->withQueryString();

        $citas = Cita::with(['medico'])
            ->where('id_paciente', $paciente->id_paciente)
            ->orderByDesc('fecha_cita')
            ->orderByDesc('hora_inicio')
            ->limit(20)
            ->get();

        return view('historial.show', compact('paciente', 'consultas', 'citas'));
    }

    public function create(Paciente $paciente)
    {
        $citasDisponibles = Cita::query()
            ->where('id_paciente', $paciente->id_paciente)
            ->whereNotIn('estado_cita', ['cancelada'])
            ->whereDoesntHave('consultaMedica')
            ->orderByDesc('fecha_cita')
            ->orderByDesc('hora_inicio')
            ->get();

        return view('historial.create', compact('paciente', 'citasDisponibles'));
    }

    public function store(Request $request, Paciente $paciente)
    {
        $data = $request->validate([
            'id_cita' => ['required', 'exists:citas,id_cita', 'unique:consultas_medicas,id_cita'],
            'fecha_consulta' => ['nullable', 'date'],
            'motivo_consulta' => ['nullable', 'string'],
            'sintomas' => ['nullable', 'string'],
            'diagnostico' => ['nullable', 'string'],
            'tratamiento' => ['nullable', 'string'],
            'receta' => ['nullable', 'string'],
            'observaciones_medicas' => ['nullable', 'string'],
            'peso' => ['nullable', 'numeric'],
            'talla' => ['nullable', 'numeric'],
            'presion_arterial' => ['nullable', 'string', 'max:20'],
            'temperatura' => ['nullable', 'numeric'],
        ]);

        $cita = Cita::with('medico')->findOrFail($data['id_cita']);

        if ((int) $cita->id_paciente !== (int) $paciente->id_paciente) {
            abort(403);
        }

        $data['id_paciente'] = $paciente->id_paciente;
        $data['id_medico'] = $cita->id_medico;
        $data['fecha_consulta'] = $data['fecha_consulta'] ?? now();

        ConsultaMedica::create($data);

        return redirect()
            ->route('historial.show', $paciente)
            ->with('success', 'Consulta médica registrada correctamente.');
    }

    public function edit(ConsultaMedica $consulta)
    {
        $consulta->load(['paciente', 'cita', 'medico']);

        return view('historial.edit', compact('consulta'));
    }

    public function update(Request $request, ConsultaMedica $consulta)
    {
        $data = $request->validate([
            'fecha_consulta' => ['nullable', 'date'],
            'motivo_consulta' => ['nullable', 'string'],
            'sintomas' => ['nullable', 'string'],
            'diagnostico' => ['nullable', 'string'],
            'tratamiento' => ['nullable', 'string'],
            'receta' => ['nullable', 'string'],
            'observaciones_medicas' => ['nullable', 'string'],
            'peso' => ['nullable', 'numeric'],
            'talla' => ['nullable', 'numeric'],
            'presion_arterial' => ['nullable', 'string', 'max:20'],
            'temperatura' => ['nullable', 'numeric'],
        ]);

        $consulta->update($data);

        return redirect()
            ->route('historial.show', $consulta->paciente)
            ->with('success', 'Consulta médica actualizada correctamente.');
    }
}
