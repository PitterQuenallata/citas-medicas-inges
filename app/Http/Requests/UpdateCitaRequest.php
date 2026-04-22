<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_paciente'     => ['required', 'integer', 'exists:pacientes,id_paciente'],
            'id_medico'       => ['required', 'integer', 'exists:medicos,id_medico'],
            'fecha_cita'      => ['required', 'date'],
            'hora_inicio'     => ['required', 'date_format:H:i'],
            'hora_fin'        => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'motivo_consulta' => ['nullable', 'string', 'max:1000'],
            'observaciones'   => ['nullable', 'string', 'max:1000'],
            'estado_cita'     => ['nullable', 'in:pendiente,confirmada,atendida,cancelada,reprogramada,no_asistio'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_paciente.required'    => 'Debe seleccionar un paciente.',
            'id_paciente.exists'      => 'El paciente seleccionado no existe.',
            'id_medico.required'      => 'Debe seleccionar un médico.',
            'id_medico.exists'        => 'El médico seleccionado no existe.',
            'fecha_cita.required'     => 'La fecha de la cita es obligatoria.',
            'hora_inicio.required'    => 'La hora de inicio es obligatoria.',
            'hora_fin.required'       => 'La hora de fin es obligatoria.',
            'hora_fin.after'          => 'La hora de fin debe ser posterior a la hora de inicio.',
            'estado_cita.in'          => 'El estado seleccionado no es válido.',
        ];
    }
}
