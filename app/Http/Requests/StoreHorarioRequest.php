<?php

namespace App\Http\Requests;

use App\Models\HorarioMedico;
use Illuminate\Foundation\Http\FormRequest;

// NUEVO: Form Request para creación de horarios médicos
class StoreHorarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_medico'             => ['required', 'exists:medicos,id_medico'],
            'dia_semana'            => ['required', 'integer', 'between:1,7'],
            'hora_inicio'           => ['required', 'date_format:H:i'],
            'hora_fin'              => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'duracion_cita_minutos' => ['required', 'integer', 'between:5,480'],
            'activo'                => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_medico.required'             => 'Debe seleccionar un médico.',
            'id_medico.exists'               => 'El médico seleccionado no existe.',
            'dia_semana.required'            => 'Debe seleccionar un día de la semana.',
            'dia_semana.between'             => 'El día debe estar entre 1 (Lunes) y 7 (Domingo).',
            'hora_inicio.required'           => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format'        => 'El formato de hora de inicio es inválido (HH:MM).',
            'hora_fin.required'              => 'La hora de fin es obligatoria.',
            'hora_fin.date_format'           => 'El formato de hora de fin es inválido (HH:MM).',
            'hora_fin.after'                 => 'La hora de fin debe ser posterior a la hora de inicio.',
            'duracion_cita_minutos.required' => 'La duración de la cita es obligatoria.',
            'duracion_cita_minutos.between'  => 'La duración debe estar entre 5 y 480 minutos.',
        ];
    }

    /**
     * Devuelve el nombre amigable del día para mensajes de error de solapamiento.
     */
    public function nombreDia(): string
    {
        $dia = (int) $this->input('dia_semana');
        return HorarioMedico::DIAS[$dia] ?? "día $dia";
    }
}
