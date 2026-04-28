<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultaMedica extends Model
{
    use HasFactory;

    protected $table = 'consultas_medicas';
    protected $primaryKey = 'id_consulta';

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

    protected $casts = [
        'fecha_consulta' => 'datetime',
        'peso' => 'decimal:2',
        'talla' => 'decimal:2',
        'temperatura' => 'decimal:2',
    ];

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }
}
