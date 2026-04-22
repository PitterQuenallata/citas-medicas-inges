<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table      = 'pacientes';
    protected $primaryKey = 'id_paciente';

    protected $fillable = [
        'id_usuario', 'codigo_paciente', 'nombres', 'apellidos',
        'fecha_nacimiento', 'sexo', 'ci', 'direccion', 'telefono',
        'email', 'grupo_sanguineo', 'contacto_emergencia_nombre',
        'contacto_emergencia_telefono', 'alergias', 'observaciones_generales', 'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_paciente', 'id_paciente');
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombres . ' ' . $this->apellidos;
    }
}
