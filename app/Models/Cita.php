<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    use HasFactory;

    protected $table = 'citas';

    protected $primaryKey = 'id_cita';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'codigo_cita',
        'id_paciente',
        'id_medico',
        'id_usuario_registra',
        'fecha_cita',
        'hora_inicio',
        'hora_fin',
        'motivo_consulta',
        'estado_cita',
        'observaciones',
        'fecha_cancelacion',
        'motivo_cancelacion',
        'id_cita_reprogramada_desde',
    ];

    protected function casts(): array
    {
        return [
            'fecha_cita' => 'date',
            'fecha_cancelacion' => 'datetime',
        ];
    }

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
        return $this->belongsTo(Usuario::class, 'id_usuario_registra', 'id_usuario');
    }

    public function citaReprogramadaDesde()
    {
        return $this->belongsTo(self::class, 'id_cita_reprogramada_desde', 'id_cita');
    }

    public function consultaMedica()
    {
        return $this->hasOne(ConsultaMedica::class, 'id_cita', 'id_cita');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'id_cita', 'id_cita');
    }
}
