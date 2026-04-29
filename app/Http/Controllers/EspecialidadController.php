<?php

namespace App\Http\Controllers;

use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;

        $especialidades = Especialidad::when($buscar, function ($q) use ($buscar) {
            $q->where(function ($query) use ($buscar) {
                $query->where('nombre_especialidad', 'like', "%{$buscar}%")
                    ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        })
        ->orderBy('id_especialidad', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('especialidades.index', compact('especialidades'));
    }

    public function create()
    {
        return view('especialidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_especialidad' => 'required|string|max:100|unique:especialidades,nombre_especialidad',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'nullable|in:activo,inactivo',
        ]);

        Especialidad::create([
            'nombre_especialidad' => $request->nombre_especialidad,
            'descripcion' => $request->descripcion,
            'estado' => $request->estado ?? 'activo',
        ]);

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad creada correctamente');
    }

    public function edit(Especialidad $especialidad)
    {
        return view('especialidades.edit', compact('especialidad'));
    }

    public function update(Request $request, Especialidad $especialidad)
    {
        $request->validate([
            'nombre_especialidad' => 'required|string|max:100|unique:especialidades,nombre_especialidad,' . $especialidad->id_especialidad . ',id_especialidad',
            'descripcion' => 'nullable|string|max:255',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $especialidad->update($request->only([
            'nombre_especialidad',
            'descripcion',
            'estado',
        ]));

        return redirect()->route('especialidades.index')
            ->with('success', 'Especialidad actualizada correctamente');
    }
}
