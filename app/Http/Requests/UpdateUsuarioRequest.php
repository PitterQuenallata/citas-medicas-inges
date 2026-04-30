<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'nombre' => mb_strtoupper(trim($this->nombre ?? ''), 'UTF-8'),
            'apellido' => mb_strtoupper(trim($this->apellido ?? ''), 'UTF-8'),
            'email' => mb_strtolower(trim($this->email ?? ''), 'UTF-8'),
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('usuario') ?? $this->route('id');

        return [
            'nombre'   => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'apellido' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email,' . $id],
            'telefono' => ['nullable', 'digits:8'],
            'password' => ['nullable', 'string', 'min:8'],
            'roles'    => ['array'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.regex'      => 'El nombre solo puede contener letras y espacios.',
            'apellido.regex'    => 'El apellido solo puede contener letras y espacios.',
            'telefono.digits'   => 'El teléfono debe tener exactamente 8 dígitos numéricos.',
            'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
            'email.unique'      => 'Este correo electrónico ya está registrado.',
        ];
    }
}
