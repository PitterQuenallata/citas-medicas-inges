<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medico extends Model
{
    protected $table = 'medicos';
    protected $primaryKey = 'id_medico';

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

    protected $casts = [
        'estado' => 'string',
    ];

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeBuscar($query, string $termino)
    {
        return $query->where(function ($q) use ($termino) {
            $q->where('nombres', 'like', "%{$termino}%")
              ->orWhere('apellidos', 'like', "%{$termino}%")
              ->orWhere('codigo_medico', 'like', "%{$termino}%")
              ->orWhere('matricula_profesional', 'like', "%{$termino}%")
              ->orWhere('email', 'like', "%{$termino}%");
        });
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function especialidades(): BelongsToMany
    {
        return $this->belongsToMany(
            Especialidad::class,
            'medico_especialidad',
            'id_medico',
            'id_especialidad'
        );
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(HorarioMedico::class, 'id_medico', 'id_medico');
    }

    public function horariosActivos(): HasMany
    {
        return $this->hasMany(HorarioMedico::class, 'id_medico', 'id_medico')
                    ->where('activo', true)
                    ->orderBy('dia_semana')
                    ->orderBy('hora_inicio');
    }

    public function bloqueos(): HasMany
    {
        return $this->hasMany(BloqueoMedico::class, 'id_medico', 'id_medico');
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_medico', 'id_medico');
    }

    public static function generarCodigo(): string
    {
        $ultimoCodigo = static::orderBy('id_medico', 'desc')->value('codigo_medico');
        
        if ($ultimoCodigo && preg_match('/^MED-(\d+)$/', $ultimoCodigo, $matches)) {
            $numero = (int) $matches[1];
            return 'MED-' . str_pad($numero + 1, 4, '0', STR_PAD_LEFT);
        }

        $ultimo = static::orderBy('id_medico', 'desc')->value('id_medico') ?? 0;
        return 'MED-' . str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
    }
}
