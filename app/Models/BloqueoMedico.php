<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloqueoMedico extends Model
{
    protected $table      = 'bloqueos_medicos';
    protected $primaryKey = 'id_bloqueo';

    protected $fillable = [
        'id_medico', 'fecha', 'hora_inicio', 'hora_fin', 'motivo',
    ];

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }
}
