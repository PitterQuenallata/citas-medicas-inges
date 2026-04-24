<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $primaryKey = 'id_paciente';

    public $incrementing = true;

    protected $keyType = 'int';

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
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'id_paciente', 'id_paciente');
    }
}
