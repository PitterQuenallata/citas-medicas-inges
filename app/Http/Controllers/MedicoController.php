<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use App\Models\HorarioMedico;
use App\Models\Medico;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MedicoController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = $request->input('busqueda');
        $filtroEstado = $request->input('estado');

        $medicos = Medico::with('especialidades', 'horariosActivos')
            ->when($busqueda, fn ($q) => $q->buscar($busqueda))
            ->when($filtroEstado, fn ($q) => $q->where('estado', $filtroEstado))
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(15)
            ->withQueryString();

        return view('medicos.index', compact('medicos', 'busqueda', 'filtroEstado'));
    }

    public function create()
    {
        $especialidades = Especialidad::where('estado', 'activo')
            ->orderBy('nombre_especialidad')
            ->get();

        $usuariosSinMedico = User::whereDoesntHave('medico')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        $codigoSugerido = Medico::generarCodigo();
        $diasSemana = HorarioMedico::DIAS;

        return view('medicos.create', compact(
            'especialidades',
            'usuariosSinMedico',
            'codigoSugerido',
            'diasSemana'
        ));
    }

    public function store(Request $request)
    {
        $datos = $this->validarMedico($request);

        DB::transaction(function () use ($datos) {
            $medico = Medico::create([
                'id_usuario'            => $datos['id_usuario'],
                'codigo_medico'         => $datos['codigo_medico'],
                'nombres'               => strtoupper($datos['nombres']),
                'apellidos'             => strtoupper($datos['apellidos']),
                'ci'                    => $datos['ci'] ?? null,
                'telefono'              => $datos['telefono'] ?? null,
                'email'                 => $datos['email'] ?? null,
                'matricula_profesional' => strtoupper($datos['matricula_profesional']),
                'estado'                => $datos['estado'],
            ]);

            $medico->especialidades()->sync($datos['especialidades'] ?? []);

            foreach ($datos['horarios'] ?? [] as $horario) {
                $medico->horarios()->create([
                    'dia_semana' => $horario['dia_semana'],
                    'hora_inicio' => $horario['hora_inicio'],
                    'hora_fin' => $horario['hora_fin'],
                    'duracion_cita_minutos' => $horario['duracion_cita_minutos'] ?? 30,
                    'activo' => true,
                ]);
            }

            $this->asignarRolMedico($datos['id_usuario']);
        });

        return redirect()->route('medicos.index')
            ->with('success', 'Médico registrado correctamente.');
    }

    public function edit(Medico $medico)
    {
        $medico->load('especialidades', 'horarios');

        $especialidades = Especialidad::where('estado', 'activo')
            ->orderBy('nombre_especialidad')
            ->get();

        $usuariosDisponibles = User::where(function ($q) use ($medico) {
                $q->whereDoesntHave('medico')
                  ->orWhere('id', $medico->id_usuario);
            })
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        $especialidadesSeleccionadas = $medico->especialidades->pluck('id_especialidad')->toArray();
        $diasSemana = HorarioMedico::DIAS;

        return view('medicos.edit', compact(
            'medico',
            'especialidades',
            'usuariosDisponibles',
            'especialidadesSeleccionadas',
            'diasSemana'
        ));
    }

    public function update(Request $request, Medico $medico)
    {
        $datos = $this->validarMedico($request, $medico);

        DB::transaction(function () use ($datos, $medico) {
            $medico->update([
                'id_usuario'            => $datos['id_usuario'],
                'codigo_medico'         => $datos['codigo_medico'],
                'nombres'               => strtoupper($datos['nombres']),
                'apellidos'             => strtoupper($datos['apellidos']),
                'ci'                    => $datos['ci'] ?? null,
                'telefono'              => $datos['telefono'] ?? null,
                'email'                 => $datos['email'] ?? null,
                'matricula_profesional' => strtoupper($datos['matricula_profesional']),
                'estado'                => $datos['estado'],
            ]);

            $medico->especialidades()->sync($datos['especialidades'] ?? []);

            $medico->horarios()->delete();

            foreach ($datos['horarios'] ?? [] as $horario) {
                $medico->horarios()->create([
                    'dia_semana' => $horario['dia_semana'],
                    'hora_inicio' => $horario['hora_inicio'],
                    'hora_fin' => $horario['hora_fin'],
                    'duracion_cita_minutos' => $horario['duracion_cita_minutos'] ?? 30,
                    'activo' => isset($horario['activo']) ? (bool) $horario['activo'] : true,
                ]);
            }

            $this->asignarRolMedico($datos['id_usuario']);
        });

        return redirect()->route('medicos.index')
            ->with('success', 'Médico actualizado correctamente.');
    }

    public function show(Medico $medico)
    {
        $medico->load('especialidades', 'horariosActivos', 'usuario', 'citas.paciente');
        $diasSemana = HorarioMedico::DIAS;
        $totalCitas = $medico->citas()->count();
        $citasAtendidas = $medico->citas()->where('estado_cita', 'atendida')->count();
        $citasPendientes = $medico->citas()->where('estado_cita', 'pendiente')->count();
        $citasHoy = $medico->citas()->whereDate('fecha_cita', today())->count();

        return view('medicos.show', compact('medico', 'diasSemana', 'totalCitas', 'citasAtendidas', 'citasPendientes', 'citasHoy'));
    }

    // NUEVO: vista de horarios agrupados por día para un médico específico
    public function horarios(Medico $medico)
    {
        $medico->load('horarios');
        $diasSemana     = HorarioMedico::DIAS;
        $horariosPorDia = $medico->horarios->groupBy('dia_semana')->sortKeys();

        return view('medicos.horarios', compact('medico', 'diasSemana', 'horariosPorDia'));
    }

    public function desactivar(Medico $medico)
    {
        $medico->update(['estado' => 'inactivo']);

        return redirect()->route('medicos.index')
            ->with('success', 'Médico desactivado correctamente.');
    }

    public function activar(Medico $medico)
    {
        $medico->update(['estado' => 'activo']);

        return redirect()->route('medicos.index')
            ->with('success', 'Médico activado correctamente.');
    }

    private function asignarRolMedico(int $idUsuario): void
    {
        $rolMedico = Rol::where('nombre_rol', 'Medico')->first();
        if ($rolMedico) {
            $usuario = User::find($idUsuario);
            $usuario?->roles()->syncWithoutDetaching([$rolMedico->id_rol]);
        }
    }

    private function validarMedico(Request $request, ?Medico $medico = null): array
    {
        $rules = [
            'id_usuario' => [
                'required',
                'exists:users,id',
                $medico
                    ? Rule::unique('medicos', 'id_usuario')->ignore($medico->id_medico, 'id_medico')
                    : 'unique:medicos,id_usuario',
            ],
            'codigo_medico' => [
                'required',
                'string',
                'max:30',
                $medico
                    ? Rule::unique('medicos', 'codigo_medico')->ignore($medico->id_medico, 'id_medico')
                    : 'unique:medicos,codigo_medico',
            ],
            'nombres'   => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'apellidos' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'ci'        => ['nullable', 'digits_between:8,10'],
            'telefono'  => ['nullable', 'digits_between:9,10'],
            'email'     => ['nullable', 'email', 'max:150'],
            'matricula_profesional' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z]+-\d+$/',
                $medico
                    ? Rule::unique('medicos', 'matricula_profesional')->ignore($medico->id_medico, 'id_medico')
                    : 'unique:medicos,matricula_profesional',
            ],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
            'especialidades' => ['nullable', 'array'],
            'especialidades.*' => ['exists:especialidades,id_especialidad'],
            'horarios' => ['nullable', 'array'],
            'horarios.*.dia_semana' => ['required_with:horarios', 'integer', 'between:1,7'],
            'horarios.*.hora_inicio' => ['required_with:horarios', 'date_format:H:i'],
            'horarios.*.hora_fin' => ['required_with:horarios', 'date_format:H:i'],
            'horarios.*.duracion_cita_minutos' => ['nullable', 'integer', 'between:5,240'],
        ];

        $messages = [
            'nombres.regex'               => 'Los nombres solo pueden contener letras y espacios.',
            'apellidos.regex'             => 'Los apellidos solo pueden contener letras y espacios.',
            'ci.digits_between'           => 'La cédula debe tener entre 8 y 10 dígitos numéricos, sin guiones ni espacios.',
            'telefono.digits_between'     => 'El teléfono debe tener entre 9 y 10 dígitos numéricos, sin espacios ni símbolos.',
            'matricula_profesional.regex' => 'La matrícula debe tener el formato LETRAS-NÚMEROS, en mayúsculas (ej: MED-12345).',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $validator->after(function ($validator) use ($request) {
            $horarios = $request->input('horarios', []);

            // Validación individual: hora_fin > hora_inicio
            foreach ($horarios as $i => $horario) {
                $inicio = $horario['hora_inicio'] ?? null;
                $fin    = $horario['hora_fin']    ?? null;

                if ($inicio && $fin && $fin <= $inicio) {
                    $validator->errors()->add(
                        "horarios.$i.hora_fin",
                        'La hora fin debe ser mayor a la hora inicio.'
                    );
                }
            }

            // NUEVO: detectar solapamientos entre filas del mismo día dentro del array enviado
            $porDia = [];
            foreach ($horarios as $i => $horario) {
                $dia    = isset($horario['dia_semana']) ? (int) $horario['dia_semana'] : null;
                $inicio = $horario['hora_inicio'] ?? null;
                $fin    = $horario['hora_fin']    ?? null;

                // Saltear filas inválidas (ya capturadas arriba)
                if ($dia === null || !$inicio || !$fin || $fin <= $inicio) {
                    continue;
                }

                // Normalizar a HH:MM:SS igual que en HorarioController::existeSolapamiento()
                $inicioNorm = strlen($inicio) === 5 ? $inicio . ':00' : $inicio;
                $finNorm    = strlen($fin)    === 5 ? $fin    . ':00' : $fin;

                // Comparar contra cada fila ya procesada del mismo día
                foreach ($porDia[$dia] ?? [] as [$j, $jInicio, $jFin]) {
                    // Dos intervalos [a,b) y [c,d) se solapan si a < d && b > c
                    if ($inicioNorm < $jFin && $finNorm > $jInicio) {
                        $nombreDia = HorarioMedico::DIAS[$dia] ?? "día $dia";
                        $validator->errors()->add(
                            "horarios.$i.hora_inicio",
                            "El horario del {$nombreDia} (fila " . ($i + 1) . ") se solapa con otro del mismo día (fila " . ($j + 1) . ")."
                        );
                    }
                }

                $porDia[$dia][] = [$i, $inicioNorm, $finNorm];
            }
        });

        return $validator->validate();
    }
}