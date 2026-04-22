<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medico extends Model
{
    // ─── Clave primaria personalizada (BD compartida usa id_medico) ─────────
    protected $primaryKey = 'id_medico';

    // ─── Campos asignables masivamente ──────────────────────────────────────
    protected $fillable = [
        'id_usuario',
        'codigo_medico',
        'nombres',
        'apellidos',
        'ci',
        'telefono',
        'email',
        'matricula_profesional',
        'estado',
    ];

    // ─── Casts de tipo ───────────────────────────────────────────────────────
    protected $casts = [
        'estado' => 'string',
    ];

    // ─── Atributo calculado: nombre completo ─────────────────────────────────
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    // ─── Scope: solo médicos activos ─────────────────────────────────────────
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    // ─── Scope: búsqueda por nombre, apellido, código o matrícula ────────────
    public function scopeBuscar($query, string $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombres',              'like', "%{$termino}%")
              ->orWhere('apellidos',           'like', "%{$termino}%")
              ->orWhere('codigo_medico',       'like', "%{$termino}%")
              ->orWhere('matricula_profesional','like', "%{$termino}%")
              ->orWhere('email',               'like', "%{$termino}%");
        });
    }

    // ─── Relaciones ──────────────────────────────────────────────────────────

    /** El usuario de sistema que corresponde a este médico (1:1). */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /** Especialidades que posee el médico (N:M). */
    public function especialidades(): BelongsToMany
    {
        return $this->belongsToMany(
            Especialidad::class,
            'medico_especialidad',
            'id_medico',
            'id_especialidad'
        );
    }

    /** Horarios semanales del médico. */
    public function horarios(): HasMany
    {
        return $this->hasMany(HorarioMedico::class, 'id_medico', 'id_medico');
    }

    /** Solo los horarios activos del médico. */
    public function horariosActivos(): HasMany
    {
        return $this->hasMany(HorarioMedico::class, 'id_medico', 'id_medico')
                    ->where('activo', true)
                    ->orderBy('dia_semana')
                    ->orderBy('hora_inicio');
    }

    /** Bloqueos de agenda del médico. */
    public function bloqueos(): HasMany
    {
        return $this->hasMany(BloqueoMedico::class, 'id_medico', 'id_medico');
    }

    /** Citas asignadas al médico. */
    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_medico', 'id_medico');
    }

    // ─── Generador de código único ───────────────────────────────────────────
    /**
     * Genera el próximo código de médico disponible.
     * Formato: MED-XXXX (ej: MED-0001)
     */
    public static function generarCodigo(): string
    {
        $ultimo = static::orderBy('id_medico', 'desc')->value('id_medico') ?? 0;
        return 'MED-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }
}
