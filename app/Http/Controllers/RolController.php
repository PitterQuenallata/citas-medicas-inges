<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Models\Permiso;
use App\Models\Auditoria;
use Illuminate\Http\Request;

class RolController extends Controller
{
    public function index()
    {
        $roles = Rol::with('permisos')
            ->where('nombre_rol', '!=', 'SuperAdmin')
            ->orderBy('id_rol')->get();
        $permisos = Permiso::orderBy('modulo')->get();
        return view('roles.index', compact('roles', 'permisos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_rol'  => 'required|string|max:50|unique:roles,nombre_rol',
            'descripcion' => 'nullable|string|max:255',
            'permisos'    => 'array',
        ]);

        $rol = Rol::create([
            'nombre_rol'  => $request->nombre_rol,
            'descripcion' => $request->descripcion,
            'estado'      => 'activo',
        ]);

        $rol->permisos()->sync($request->permisos ?? []);

        Auditoria::registrar('crear', 'roles', $rol->id_rol, null, $rol->toArray());

        return redirect()->route('roles.index')->with('swal_success', 'Rol creado correctamente');
    }

    public function update(Request $request, $id)
    {
        $rol = Rol::findOrFail($id);
        $datosAnteriores = $rol->toArray();

        $request->validate([
            'nombre_rol'  => 'required|string|max:50|unique:roles,nombre_rol,' . $id . ',id_rol',
            'descripcion' => 'nullable|string|max:255',
            'permisos'    => 'array',
        ]);

        $rol->update([
            'nombre_rol'  => $request->nombre_rol,
            'descripcion' => $request->descripcion,
        ]);

        $rol->permisos()->sync($request->permisos ?? []);

        Auditoria::registrar('editar', 'roles', $rol->id_rol, $datosAnteriores, $rol->fresh()->toArray());

        return redirect()->route('roles.index')->with('swal_success', 'Rol actualizado correctamente');
    }

    public function destroy($id)
    {
        $rol = Rol::findOrFail($id);

        if ($rol->usuarios()->count() > 0) {
            return redirect()->route('roles.index')->with('swal_error', 'No se puede eliminar un rol con usuarios asignados');
        }

        $datosAnteriores = $rol->toArray();
        $rol->permisos()->detach();
        $rol->delete();

        Auditoria::registrar('eliminar', 'roles', $id, $datosAnteriores, null);

        return redirect()->route('roles.index')->with('swal_success', 'Rol eliminado correctamente');
    }
}
