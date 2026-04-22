<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
   protected $primaryKey = 'id_paciente';

    protected $fillable = [
        'id_usuario',
        'codigo_paciente',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'ci',
        'direccion',
        'telefono',
        'email',
        'grupo_sanguineo',
        'contacto_emergencia_nombre',
        'contacto_emergencia_telefono',
        'alergias',
        'observaciones_generales',
        'estado'
    ];
}