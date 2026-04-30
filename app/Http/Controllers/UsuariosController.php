<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUsuarioRequest;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $esSuperAdmin = Auth::user()->esSuperAdmin();

        $usuarios = User::with('roles')
            ->when(!$esSuperAdmin, function ($q) {
                $q->where('estado', '!=', 'eliminado');
            })
            ->when(!$esSuperAdmin, function ($q) {
                $q->whereDoesntHave('roles', fn($r) => $r->where('nombre_rol', 'SuperAdmin'));
            })
            ->when($buscar, function ($q) use ($buscar) {
                $q->where(function ($query) use ($buscar) {
                    $query->where('nombre', 'like', "%$buscar%")
                        ->orWhere('apellido', 'like', "%$buscar%")
                        ->orWhere('email', 'like', "%$buscar%");
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $roles = Rol::where('estado', 'activo')
            ->where('nombre_rol', '!=', 'SuperAdmin')
            ->orderBy('nombre_rol')->get();

        return view('usuarios.index', compact('usuarios', 'roles', 'buscar', 'esSuperAdmin'));
    }

    public function store(StoreUsuarioRequest $request)
    {
        $usuario = User::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'telefono' => $request->telefono,
            'password' => $request->password,
            'estado'   => 'activo',
        ]);

        $usuario->roles()->sync($request->roles ?? []);

        Auditoria::registrar('crear', 'users', $usuario->id, null, $usuario->toArray());

        return redirect()->route('usuarios.index')->with('swal_success', 'Usuario creado correctamente');
    }

    public function update(UpdateUsuarioRequest $request, $id)
    {
        $usuario = User::findOrFail($id);
        $datosAnteriores = $usuario->toArray();

        $datos = [
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'telefono' => $request->telefono,
        ];

        if ($request->filled('password')) {
            $datos['password'] = $request->password;
        }

        $usuario->update($datos);
        $usuario->roles()->sync($request->roles ?? []);

        Auditoria::registrar('editar', 'users', $usuario->id, $datosAnteriores, $usuario->fresh()->toArray());

        return redirect()->route('usuarios.index')->with('swal_success', 'Usuario actualizado correctamente');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $datosAnteriores = $usuario->toArray();

        if ($usuario->tieneRelaciones()) {
            $usuario->update(['estado' => 'eliminado']);
            Auditoria::registrar('eliminar_logico', 'users', $usuario->id, $datosAnteriores, ['estado' => 'eliminado']);
            return redirect()->route('usuarios.index')->with('swal_success', 'Usuario eliminado logicamente (tiene datos asociados)');
        }

        $usuario->roles()->detach();
        $usuario->delete();
        Auditoria::registrar('eliminar', 'users', $id, $datosAnteriores, null);
        return redirect()->route('usuarios.index')->with('swal_success', 'Usuario eliminado permanentemente');
    }

    public function activar($id)
    {
        $usuario = User::findOrFail($id);
        $estadoAnterior = $usuario->estado;
        $usuario->update(['estado' => 'activo']);

        Auditoria::registrar('activar', 'users', $usuario->id, ['estado' => $estadoAnterior], ['estado' => 'activo']);

        return redirect()->route('usuarios.index')->with('swal_success', 'Usuario activado correctamente');
    }
}
