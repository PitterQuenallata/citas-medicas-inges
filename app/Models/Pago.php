<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table      = 'pagos';
    protected $primaryKey = 'id_pago';

    public function getRouteKeyName()
    {
        return 'id_pago';
    }

    protected $fillable = [
        'id_cita', 'codigo_pago', 'monto', 'metodo_pago', 'estado_pago',
        'referencia_externa', 'datos_remitente', 'comprobante_url',
        'fecha_pago', 'id_usuario_registra', 'observaciones',
    ];

    protected $casts = [
        'monto'           => 'decimal:2',
        'datos_remitente' => 'array',
        'fecha_pago'      => 'datetime',
    ];

    // ── Relaciones ───────────────────────────────────────────────────

    public function cita()
    {
        return $this->belongsTo(Cita::class, 'id_cita', 'id_cita');
    }

    public function usuarioRegistra()
    {
        return $this->belongsTo(User::class, 'id_usuario_registra', 'id');
    }

    // ── Accessors ────────────────────────────────────────────────────

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado_pago) {
            'pendiente' => 'Pendiente',
            'pagado'    => 'Pagado',
            'anulado'   => 'Anulado',
            default     => $this->estado_pago,
        };
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->estado_pago) {
            'pendiente' => 'bg-warning/10 text-warning',
            'pagado'    => 'bg-success/10 text-success',
            'anulado'   => 'bg-error/10 text-error',
            default     => 'bg-slate-100 text-slate-600',
        };
    }

    public function getMetodoLabelAttribute(): string
    {
        return match ($this->metodo_pago) {
            'qr'            => 'QR VeriPagos',
            'efectivo'      => 'Efectivo',
            'transferencia' => 'Transferencia',
            default         => $this->metodo_pago,
        };
    }

    // ── Generador de código ──────────────────────────────────────────

    public static function generarCodigo(): string
    {
        $ultimo = static::max('id_pago') ?? 0;
        return 'PAG-' . str_pad($ultimo + 1, 6, '0', STR_PAD_LEFT);
    }
}
