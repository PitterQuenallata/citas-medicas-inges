<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    protected $table = 'medicos';

    protected $primaryKey = 'id_medico';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_usuario',
        'codigo_medico',
        'nombres',
        'apellidos',
        'ci',
        'telefono',
        'email',
        'matricula_profesional',
        'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function especialidades()
    {
        return $this->belongsToMany(Especialidad::class, 'medico_especialidad', 'id_medico', 'id_especialidad');
    }

    public function horarios()
    {
        return $this->hasMany(HorarioMedico::class, 'id_medico', 'id_medico');
    }

    public function bloqueos()
    {
        return $this->hasMany(BloqueoMedico::class, 'id_medico', 'id_medico');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_medico', 'id_medico');
    }
}
