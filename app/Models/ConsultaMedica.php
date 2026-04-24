<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultaMedica extends Model
{
    use HasFactory;

    protected $table = 'consultas_medicas';

    protected $primaryKey = 'id_consulta';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_cita',
        'id_paciente',
        'id_medico',
        'fecha_consulta',
        'motivo_consulta',
        'sintomas',
        'diagnostico',
        'tratamiento',
        'receta',
        'observaciones_medicas',
        'peso',
        'talla',
        'presion_arterial',
        'temperatura',
    ];

    protected function casts(): array
    {
        return [
            'fecha_consulta' => 'datetime',
        ];
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }
}
