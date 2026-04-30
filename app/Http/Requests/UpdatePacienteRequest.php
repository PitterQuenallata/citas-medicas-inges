<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'nombres' => mb_strtoupper(trim($this->nombres ?? ''), 'UTF-8'),
            'apellidos' => mb_strtoupper(trim($this->apellidos ?? ''), 'UTF-8'),
            'email' => mb_strtolower(trim($this->email ?? ''), 'UTF-8'),
            'contacto_emergencia_nombre' => mb_strtoupper(trim($this->contacto_emergencia_nombre ?? ''), 'UTF-8'),
        ]);
    }

    public function rules(): array
    {
        // En PacienteController, el parámetro de la ruta es 'paciente' y es una instancia de modelo.
        // Pero en la validación inline usamos: $paciente->id_paciente
        $id = $this->route('paciente') ? $this->route('paciente')->id_paciente : null;

        return [
            'nombres' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'apellidos' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'fecha_nacimiento' => ['required', 'date'],
            'sexo' => ['required', 'in:masculino,femenino,otro'],
            'ci' => ['required', 'regex:/^[0-9]+$/', 'unique:pacientes,ci,' . $id . ',id_paciente'],
            'direccion' => ['required', 'string'],
            'telefono' => ['required', 'digits:8', 'regex:/^[678][0-9]{7}$/'],
            'email' => ['required', 'email', 'max:150'],
            'grupo_sanguineo' => ['required', 'string'],
            'contacto_emergencia_nombre' => ['required', 'string', 'max:100', 'regex:/^[\pL\s]+$/u'],
            'contacto_emergencia_telefono' => ['required', 'digits:8', 'regex:/^[678][0-9]{7}$/'],
            'alergias' => ['required', 'string'],
            'observaciones_generales' => ['required', 'string'],
            'estado' => ['nullable', 'string', 'in:activo,inactivo'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombres.regex' => 'Los nombres solo pueden contener letras y espacios.',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios.',
            'ci.unique' => 'Este CI ya está registrado.',
            'ci.regex' => 'El CI debe contener solo números.',
            'telefono.digits' => 'El teléfono debe tener exactamente 8 dígitos.',
            'telefono.regex' => 'El teléfono debe empezar con 6, 7 u 8.',
            'contacto_emergencia_nombre.regex' => 'El contacto de emergencia solo puede contener letras y espacios.',
            'contacto_emergencia_telefono.digits' => 'El teléfono de emergencia debe tener exactamente 8 dígitos.',
            'contacto_emergencia_telefono.regex' => 'El teléfono de emergencia debe empezar con 6, 7 u 8.',
        ];
    }
}
