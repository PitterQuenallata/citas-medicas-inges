<?php

namespace App\Http\Controllers;

use App\Models\HorarioMedico;
use App\Models\Medico;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;

        $horarios = HorarioMedico::with('medico')
            ->when($buscar, function ($q) use ($buscar) {
                $q->whereHas('medico', function ($m) use ($buscar) {
                    $m->where('nombres', 'like', "%{$buscar}%")
                        ->orWhere('apellidos', 'like', "%{$buscar}%")
                        ->orWhere('codigo_medico', 'like', "%{$buscar}%");
                });
            })
            ->orderBy('id_horario', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('horarios.index', compact('horarios'));
    }

    public function create()
    {
        $medicos = Medico::orderBy('apellidos')->orderBy('nombres')->get();
        $dias = HorarioMedico::DIAS;

        return view('horarios.create', compact('medicos', 'dias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_medico' => 'required|exists:medicos,id_medico',
            'dia_semana' => 'required|integer|min:1|max:7',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'duracion_cita_minutos' => 'required|integer|min:5|max:480',
            'activo' => 'nullable|boolean',
        ]);

        HorarioMedico::create([
            'id_medico' => $request->id_medico,
            'dia_semana' => $request->dia_semana,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'duracion_cita_minutos' => $request->duracion_cita_minutos,
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()->route('horarios.index')
            ->with('success', 'Horario creado correctamente');
    }

    public function edit(HorarioMedico $horario)
    {
        $medicos = Medico::orderBy('apellidos')->orderBy('nombres')->get();
        $dias = HorarioMedico::DIAS;

        return view('horarios.edit', compact('horario', 'medicos', 'dias'));
    }

    public function update(Request $request, HorarioMedico $horario)
    {
        $request->validate([
            'id_medico' => 'required|exists:medicos,id_medico',
            'dia_semana' => 'required|integer|min:1|max:7',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'duracion_cita_minutos' => 'required|integer|min:5|max:480',
            'activo' => 'nullable|boolean',
        ]);

        $horario->update([
            'id_medico' => $request->id_medico,
            'dia_semana' => $request->dia_semana,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'duracion_cita_minutos' => $request->duracion_cita_minutos,
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()->route('horarios.index')
            ->with('success', 'Horario actualizado correctamente');
    }
}
