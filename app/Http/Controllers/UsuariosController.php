<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;

        $usuarios = User::with('roles')
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

        $roles = Rol::where('estado', 'activo')->orderBy('nombre_rol')->get();

        return view('usuarios.index', compact('usuarios', 'roles', 'buscar'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'roles'    => 'array',
        ]);

        $usuario = User::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'email'    => $request->email,
            'telefono' => $request->telefono,
            'password' => $request->password,
            'estado'   => 'activo',
        ]);

        $usuario->roles()->sync($request->roles ?? []);

        return redirect()->route('usuarios.index')->with('swal_success', 'Usuario creado correctamente');
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email'    => 'required|email|max:150|unique:users,email,' . $id,
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'roles'    => 'array',
        ]);

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

        return redirect()->route('usuarios.index')->with('swal_success', 'Usuario actualizado correctamente');
    }

    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['estado' => 'inactivo']);
        return redirect()->route('usuarios.index')->with('swal_success', 'Usuario desactivado');
    }

    public function activar($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['estado' => 'activo']);
        return redirect()->route('usuarios.index')->with('swal_success', 'Usuario activado');
    }
}
