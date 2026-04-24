<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';
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
        'estado',
    ];

    protected static function booted(): void
    {
        static::created(function ($paciente) {
            $paciente->updateQuietly([
                'codigo_paciente' => 'PAC-' . str_pad($paciente->id_paciente, 4, '0', STR_PAD_LEFT),
            ]);
        });
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_paciente', 'id_paciente');
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombres . ' ' . $this->apellidos;
    }
}
