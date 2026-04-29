<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table      = 'notificaciones';
    protected $primaryKey = 'id_notificacion';

    protected $fillable = [
        'id_cita',
        'id_paciente',
        'tipo_notificacion',
        'canal',
        'mensaje',
        'fecha_envio',
        'estado_envio',
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
    ];

    public function cita(): BelongsTo
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function getEstadoBadgeClassAttribute(): string
    {
        return match ($this->estado_envio) {
            'enviado'   => 'bg-success/10 text-success',
            'fallido'   => 'bg-error/10 text-error',
            'pendiente' => 'bg-warning/10 text-warning',
            default     => 'bg-slate-100 text-slate-500',
        };
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo_notificacion) {
            'reserva'        => 'Reserva',
            'confirmacion'   => 'Confirmación',
            'cancelacion'    => 'Cancelación',
            'reprogramacion' => 'Reprogramación',
            'recordatorio'   => 'Recordatorio',
            default          => $this->tipo_notificacion,
        };
    }
}
