<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioMedico extends Model
{
    protected $table      = 'horarios_medicos';
    protected $primaryKey = 'id_horario';

    protected $fillable = [
        'id_medico', 'dia_semana', 'hora_inicio',
        'hora_fin', 'duracion_cita_minutos', 'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }
}
