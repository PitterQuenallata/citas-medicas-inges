<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioMedico extends Model
{
    protected $table = 'horarios_medicos';
    protected $primaryKey = 'id_horario';

    protected $fillable = [
        'id_medico',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'duracion_cita_minutos',
        'activo',
    ];

    protected $casts = [
        'dia_semana' => 'integer',
        'duracion_cita_minutos' => 'integer',
        'activo' => 'boolean',
    ];

    public const DIAS = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo',
    ];

    public function getNombreDiaAttribute(): string
    {
        return self::DIAS[$this->dia_semana] ?? 'Desconocido';
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }
}
