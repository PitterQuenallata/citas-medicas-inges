<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;

class CitaController extends Controller
{
    public function index()
    {
        $citas = Cita::query()
            ->with(['paciente', 'medico'])
            ->orderBy('id_cita', 'desc')
            ->paginate(10);

        return view('citas.index', compact('citas'));
    }

    public function create()
    {
        $pacientes = Paciente::query()->orderBy('apellidos')->orderBy('nombres')->get();
        $medicos = Medico::query()->orderBy('apellidos')->orderBy('nombres')->get();

        return view('citas.create', compact('pacientes', 'medicos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo_cita' => ['required', 'string', 'max:30', 'unique:citas,codigo_cita'],
            'id_paciente' => ['required', 'integer', 'exists:pacientes,id_paciente'],
            'id_medico' => ['required', 'integer', 'exists:medicos,id_medico'],
            'fecha_cita' => ['required', 'date'],
            'hora_inicio' => ['required'],
            'hora_fin' => ['required'],
            'motivo_consulta' => ['nullable', 'string'],
            'estado_cita' => ['required', 'in:pendiente,confirmada,atendida,cancelada,reprogramada,no_asistio'],
            'observaciones' => ['nullable', 'string'],
            'motivo_cancelacion' => ['nullable', 'string', 'max:255'],
            'fecha_cancelacion' => ['nullable', 'date'],
            'id_cita_reprogramada_desde' => ['nullable', 'integer'],
        ]);

        $data['id_usuario_registra'] = auth()->id();

        Cita::query()->create($data);

        return redirect()->route('citas.index');
    }

    public function edit(Cita $cita)
    {
        $pacientes = Paciente::query()->orderBy('apellidos')->orderBy('nombres')->get();
        $medicos = Medico::query()->orderBy('apellidos')->orderBy('nombres')->get();

        return view('citas.edit', compact('cita', 'pacientes', 'medicos'));
    }

    public function update(Request $request, Cita $cita)
    {
        $data = $request->validate([
            'codigo_cita' => ['required', 'string', 'max:30', 'unique:citas,codigo_cita,' . $cita->id_cita . ',id_cita'],
            'id_paciente' => ['required', 'integer', 'exists:pacientes,id_paciente'],
            'id_medico' => ['required', 'integer', 'exists:medicos,id_medico'],
            'fecha_cita' => ['required', 'date'],
            'hora_inicio' => ['required'],
            'hora_fin' => ['required'],
            'motivo_consulta' => ['nullable', 'string'],
            'estado_cita' => ['required', 'in:pendiente,confirmada,atendida,cancelada,reprogramada,no_asistio'],
            'observaciones' => ['nullable', 'string'],
            'motivo_cancelacion' => ['nullable', 'string', 'max:255'],
            'fecha_cancelacion' => ['nullable', 'date'],
            'id_cita_reprogramada_desde' => ['nullable', 'integer'],
        ]);

        $cita->update($data);

        return redirect()->route('citas.index');
    }

    public function destroy(Cita $cita)
    {
        $cita->delete();

        return redirect()->route('citas.index');
    }
}
