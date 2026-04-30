<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// NUEVO: Form Request para creación de especialidades
class StoreEspecialidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_especialidad' => ['required', 'string', 'max:100', 'unique:especialidades,nombre_especialidad'],
            'descripcion'         => ['nullable', 'string', 'max:255'],
            'costo_consulta'      => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'estado'              => ['nullable', Rule::in(['activo', 'inactivo'])],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_especialidad.required' => 'El nombre de la especialidad es obligatorio.',
            'nombre_especialidad.unique'   => 'Ya existe una especialidad con ese nombre.',
            'nombre_especialidad.max'      => 'El nombre no puede superar 100 caracteres.',
            'costo_consulta.numeric'       => 'El costo debe ser un valor numérico.',
            'costo_consulta.min'           => 'El costo no puede ser negativo.',
            'estado.in'                    => 'El estado debe ser activo o inactivo.',
        ];
    }
}
