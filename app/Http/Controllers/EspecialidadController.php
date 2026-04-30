<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEspecialidadRequest;
use App\Http\Requests\UpdateEspecialidadRequest;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EspecialidadController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;

        $especialidades = Especialidad::withCount('medicos')
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($query) use ($buscar) {
                    $query->where('nombre_especialidad', 'like', "%{$buscar}%")
                          ->orWhere('descripcion', 'like', "%{$buscar}%");
                });
            })
            // REFACTOR: ordenar alfabéticamente es más intuitivo que por id
            ->orderBy('nombre_especialidad')
            ->paginate(10)
            ->withQueryString();

        return view('especialidades.index', compact('especialidades'));
    }

    public function create()
    {
        return view('especialidades.create');
    }

    // REFACTOR: validación movida a StoreEspecialidadRequest
    public function store(StoreEspecialidadRequest $request)
    {
        Especialidad::create([
            'nombre_especialidad' => $request->nombre_especialidad,
            'descripcion'         => $request->descripcion,
            'costo_consulta'      => $request->costo_consulta ?? 0,
            'estado'              => $request->estado ?? 'activo',
        ]);

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad creada correctamente.');
    }

    // NUEVO: detalle de una especialidad con sus médicos asociados
    public function show(Especialidad $especialidad)
    {
        $especialidad->load(['medicos' => function ($q) {
            $q->orderBy('apellidos')->orderBy('nombres');
        }]);

        return view('especialidades.show', compact('especialidad'));
    }

    public function edit(Especialidad $especialidad)
    {
        return view('especialidades.edit', compact('especialidad'));
    }

    // REFACTOR: validación movida a UpdateEspecialidadRequest
    public function update(UpdateEspecialidadRequest $request, Especialidad $especialidad)
    {
        $especialidad->update([
            'nombre_especialidad' => $request->nombre_especialidad,
            'descripcion'         => $request->descripcion,
            'costo_consulta'      => $request->costo_consulta ?? 0,
            'estado'              => $request->estado,
        ]);

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad actualizada correctamente.');
    }

    // NUEVO: eliminar especialidad con guard de FK
    public function destroy(Especialidad $especialidad)
    {
        if ($especialidad->medicos()->exists()) {
            return redirect()->route('especialidades.index')
                ->with('error', 'No se puede eliminar "' . $especialidad->nombre_especialidad . '" porque tiene médicos asociados.');
        }

        $nombre = $especialidad->nombre_especialidad;
        $especialidad->delete();

        return redirect()->route('especialidades.index')
            ->with('success', "Especialidad \"{$nombre}\" eliminada correctamente.");
    }
}
