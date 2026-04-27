<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->buscar;

        $usuarios = User::when($buscar, function ($q) use ($buscar) {
            $q->where(function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('apellido', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%")
                    ->orWhere('telefono', 'like', "%{$buscar}%");
            });
        })
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->withQueryString();

        return view('usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'estado' => 'nullable|in:activo,inactivo,bloqueado',
        ]);

        User::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => $request->password,
            'estado' => $request->estado ?? 'activo',
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente');
    }

    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $usuario->id,
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'estado' => 'required|in:activo,inactivo,bloqueado',
        ]);

        $data = $request->only([
            'nombre',
            'apellido',
            'email',
            'telefono',
            'estado',
        ]);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $usuario->update($data);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente');
    }
}
