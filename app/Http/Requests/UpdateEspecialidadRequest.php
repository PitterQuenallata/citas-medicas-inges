<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// NUEVO: Form Request para actualización de especialidades
class UpdateEspecialidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtener la especialidad del route model binding
        $especialidad = $this->route('especialidad');

        return [
            'nombre_especialidad' => [
                'required', 'string', 'max:100',
                Rule::unique('especialidades', 'nombre_especialidad')
                    ->ignore($especialidad->id_especialidad, 'id_especialidad'),
            ],
            'descripcion'    => ['nullable', 'string', 'max:255'],
            'costo_consulta' => ['nullable', 'numeric', 'min:0', 'max:99999.99'],
            'estado'         => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_especialidad.required' => 'El nombre de la especialidad es obligatorio.',
            'nombre_especialidad.unique'   => 'Ya existe otra especialidad con ese nombre.',
            'nombre_especialidad.max'      => 'El nombre no puede superar 100 caracteres.',
            'costo_consulta.numeric'       => 'El costo debe ser un valor numérico.',
            'costo_consulta.min'           => 'El costo no puede ser negativo.',
            'estado.required'              => 'El estado es obligatorio.',
            'estado.in'                    => 'El estado debe ser activo o inactivo.',
        ];
    }
}
