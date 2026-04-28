<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $table      = 'citas';
    protected $primaryKey = 'id_cita';

    public function getRouteKeyName()
    {
        return 'id_cita';
    }

    protected $fillable = [
        'codigo_cita', 'id_paciente', 'id_medico', 'id_usuario_registra',
        'fecha_cita', 'hora_inicio', 'hora_fin', 'motivo_consulta',
        'estado_cita', 'observaciones', 'fecha_cancelacion',
        'motivo_cancelacion', 'id_cita_reprogramada_desde',
    ];

    protected $casts = [
        'fecha_cita'        => 'date',
        'fecha_cancelacion' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'id_paciente', 'id_paciente');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }

    public function usuarioRegistra()
    {
        return $this->belongsTo(User::class, 'id_usuario_registra', 'id');
    }

    public function citaOriginal()
    {
        return $this->belongsTo(Cita::class, 'id_cita_reprogramada_desde', 'id_cita');
    }

    public function reprogramaciones()
    {
        return $this->hasMany(Cita::class, 'id_cita_reprogramada_desde', 'id_cita');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_cita', 'id_cita');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'id_cita', 'id_cita')
                    ->whereIn('estado_pago', ['pendiente', 'pagado'])
                    ->latest();
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_cita', 'id_cita');
    }

    public function getEstaPagadaAttribute(): bool
    {
        return $this->pago && $this->pago->estado_pago === 'pagado';
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->estado_cita) {
            'pendiente'    => 'badge-pendiente',
            'confirmada'   => 'badge-confirmada',
            'atendida'     => 'badge-atendida',
            'cancelada'    => 'badge-cancelada',
            'reprogramada' => 'badge-reprogramada',
            'no_asistio'   => 'badge-no-asistio',
            default        => 'badge-secondary',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado_cita) {
            'pendiente'    => 'Pendiente',
            'confirmada'   => 'Confirmada',
            'atendida'     => 'Atendida',
            'cancelada'    => 'Cancelada',
            'reprogramada' => 'Reprogramada',
            'no_asistio'   => 'No asistió',
            default        => $this->estado_cita,
        };
    }

    public static function generarCodigo(): string
    {
        $fecha  = now()->format('Ymd');
        $ultimo = static::whereDate('created_at', today())->count() + 1;
        return 'CIT-' . $fecha . '-' . str_pad($ultimo, 4, '0', STR_PAD_LEFT);
    }
}
