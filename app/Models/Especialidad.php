<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Especialidad extends Model
{
    protected $table = 'especialidades';
    protected $primaryKey = 'id_especialidad';

    protected $fillable = [
        'nombre_especialidad',
        'descripcion',
        'estado',
    ];

    public function medicos(): BelongsToMany
    {
        return $this->belongsToMany(
            Medico::class,
            'medico_especialidad',
            'id_especialidad',
            'id_medico'
        );
    }
}
