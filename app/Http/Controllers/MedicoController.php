<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MedicoController extends Controller
{
    // ─── Aplica middleware auth a todas las rutas del módulo ─────────────────
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // INDEX – Listado con búsqueda y paginación
    // ─────────────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $busqueda     = $request->input('busqueda');
        $filtroEstado = $request->input('estado');

        $medicos = Medico::with('especialidades', 'horariosActivos')
            ->when($busqueda, fn($q) => $q->buscar($busqueda))
            ->when($filtroEstado, fn($q) => $q->where('estado', $filtroEstado))
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(15)
            ->withQueryString();

        return view('medicos.index', compact('medicos', 'busqueda', 'filtroEstado'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE – Formulario de nuevo médico
    // ─────────────────────────────────────────────────────────────────────────
    public function create()
    {
        $especialidades = Especialidad::where('estado', 'activo')
                                      ->orderBy('nombre_especialidad')
                                      ->get();

        // Solo usuarios sin médico asignado para el select
       $usuariosSinMedico = User::whereDoesntHave('medico')
                                    ->where('estado', 'activo')
                                    ->orderBy('nombre')
                                    ->get();

        $codigoSugerido = Medico::generarCodigo();
        $diasSemana     = \App\Models\HorarioMedico::DIAS;

        return view('medicos.create', compact(
            'especialidades',
            'usuariosSinMedico',
            'codigoSugerido',
            'diasSemana'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // STORE – Persistencia del nuevo médico
    // ─────────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $datos = $request->validate([
            'id_usuario'           => ['required', 'exists:usuarios,id_usuario', 'unique:medicos,id_usuario'],
            'codigo_medico'        => ['required', 'string', 'max:30', 'unique:medicos,codigo_medico'],
            'nombres'              => ['required', 'string', 'max:100'],
            'apellidos'            => ['required', 'string', 'max:100'],
            'ci'                   => ['nullable', 'string', 'max:20'],
            'telefono'             => ['nullable', 'string', 'max:20'],
            'email'                => ['nullable', 'email', 'max:150'],
            'matricula_profesional'=> ['required', 'string', 'max:50', 'unique:medicos,matricula_profesional'],
            'estado'               => ['required', Rule::in(['activo', 'inactivo'])],
            'especialidades'       => ['nullable', 'array'],
            'especialidades.*'     => ['exists:especialidades,id_especialidad'],
            // Horarios (array de slots opcionales)
            'horarios'             => ['nullable', 'array'],
            'horarios.*.dia_semana'           => ['required_with:horarios', 'integer', 'between:1,7'],
            'horarios.*.hora_inicio'          => ['required_with:horarios', 'date_format:H:i'],
            'horarios.*.hora_fin'             => ['required_with:horarios', 'date_format:H:i', 'after:horarios.*.hora_inicio'],
            'horarios.*.duracion_cita_minutos'=> ['nullable', 'integer', 'between:5,240'],
        ]);

        DB::transaction(function () use ($datos, $request) {
            $medico = Medico::create([
                'id_usuario'            => $datos['id_usuario'],
                'codigo_medico'         => $datos['codigo_medico'],
                'nombres'               => $datos['nombres'],
                'apellidos'             => $datos['apellidos'],
                'ci'                    => $datos['ci'] ?? null,
                'telefono'              => $datos['telefono'] ?? null,
                'email'                 => $datos['email'] ?? null,
                'matricula_profesional' => $datos['matricula_profesional'],
                'estado'                => $datos['estado'],
            ]);

            // Sincronizar especialidades (N:M)
            if (!empty($datos['especialidades'])) {
                $medico->especialidades()->sync($datos['especialidades']);
            }

            // Guardar horarios
            if (!empty($datos['horarios'])) {
                foreach ($datos['horarios'] as $horario) {
                    $medico->horarios()->create([
                        'dia_semana'            => $horario['dia_semana'],
                        'hora_inicio'           => $horario['hora_inicio'],
                        'hora_fin'              => $horario['hora_fin'],
                        'duracion_cita_minutos' => $horario['duracion_cita_minutos'] ?? 30,
                        'activo'                => true,
                    ]);
                }
            }
        });

        return redirect()->route('medicos.index')
                         ->with('success', 'Médico registrado correctamente.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EDIT – Formulario de edición
    // ─────────────────────────────────────────────────────────────────────────
    public function edit(Medico $medico)
    {
        $medico->load('especialidades', 'horarios');

        $especialidades = Especialidad::where('estado', 'activo')
                                      ->orderBy('nombre_especialidad')
                                      ->get();

        // Usuarios sin médico asignado + el usuario actual del médico
        $usuariosDisponibles = User::where(function ($q) use ($medico) {
            $q->whereDoesntHave('medico')
              ->orWhere('id_usuario', $medico->id_usuario);
        })->where('estado', 'activo')->orderBy('nombre')->get();

        $especialidadesSeleccionadas = $medico->especialidades->pluck('id_especialidad')->toArray();
        $diasSemana = \App\Models\HorarioMedico::DIAS;

        return view('medicos.edit', compact(
            'medico',
            'especialidades',
            'usuariosDisponibles',
            'especialidadesSeleccionadas',
            'diasSemana'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE – Persistencia de cambios
    // ─────────────────────────────────────────────────────────────────────────
    public function update(Request $request, Medico $medico)
    {
        $datos = $request->validate([
            'id_usuario'           => ['required', 'exists:usuarios,id_usuario',
                                       Rule::unique('medicos', 'id_usuario')->ignore($medico->id_medico, 'id_medico')],
            'codigo_medico'        => ['required', 'string', 'max:30',
                                       Rule::unique('medicos', 'codigo_medico')->ignore($medico->id_medico, 'id_medico')],
            'nombres'              => ['required', 'string', 'max:100'],
            'apellidos'            => ['required', 'string', 'max:100'],
            'ci'                   => ['nullable', 'string', 'max:20'],
            'telefono'             => ['nullable', 'string', 'max:20'],
            'email'                => ['nullable', 'email', 'max:150'],
            'matricula_profesional'=> ['required', 'string', 'max:50',
                                       Rule::unique('medicos', 'matricula_profesional')->ignore($medico->id_medico, 'id_medico')],
            'estado'               => ['required', Rule::in(['activo', 'inactivo'])],
            'especialidades'       => ['nullable', 'array'],
            'especialidades.*'     => ['exists:especialidades,id_especialidad'],
            // Horarios
            'horarios'             => ['nullable', 'array'],
            'horarios.*.id_horario'           => ['nullable', 'integer'],
            'horarios.*.dia_semana'           => ['required_with:horarios', 'integer', 'between:1,7'],
            'horarios.*.hora_inicio'          => ['required_with:horarios', 'date_format:H:i'],
            'horarios.*.hora_fin'             => ['required_with:horarios', 'date_format:H:i'],
            'horarios.*.duracion_cita_minutos'=> ['nullable', 'integer', 'between:5,240'],
            'horarios.*.activo'               => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($datos, $medico) {
            $medico->update([
                'id_usuario'            => $datos['id_usuario'],
                'codigo_medico'         => $datos['codigo_medico'],
                'nombres'               => $datos['nombres'],
                'apellidos'             => $datos['apellidos'],
                'ci'                    => $datos['ci'] ?? null,
                'telefono'              => $datos['telefono'] ?? null,
                'email'                 => $datos['email'] ?? null,
                'matricula_profesional' => $datos['matricula_profesional'],
                'estado'                => $datos['estado'],
            ]);

            // Resincronizar especialidades
            $medico->especialidades()->sync($datos['especialidades'] ?? []);

            // Reconstruir horarios: borramos y volvemos a crear
            // (estrategia simple; evita lógica de "qué horario actualizar")
            $medico->horarios()->delete();
            foreach ($datos['horarios'] ?? [] as $horario) {
                $medico->horarios()->create([
                    'dia_semana'            => $horario['dia_semana'],
                    'hora_inicio'           => $horario['hora_inicio'],
                    'hora_fin'              => $horario['hora_fin'],
                    'duracion_cita_minutos' => $horario['duracion_cita_minutos'] ?? 30,
                    'activo'                => isset($horario['activo']) ? (bool)$horario['activo'] : true,
                ]);
            }
        });

        return redirect()->route('medicos.index')
                         ->with('success', 'Médico actualizado correctamente.');
    }
}
