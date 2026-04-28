<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'password',
        'estado',
        'ultimo_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'ultimo_login' => 'datetime',
        ];
    }

    public function medico(): HasOne
    {
        return $this->hasOne(Medico::class, 'id_usuario');
    }

    public function paciente(): HasOne
    {
        return $this->hasOne(Paciente::class, 'id_usuario');
    }

    public function citasRegistradas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_usuario_registra');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol', 'id_usuario', 'id_rol');
    }

    public function tienePermiso(string $permiso): bool
    {
        if ($this->esSuperAdmin()) return true;

        return $this->cargarPermisos()->contains($permiso);
    }

    public function esSuperAdmin(): bool
    {
        if (!isset($this->esSuperAdminCache)) {
            $this->esSuperAdminCache = $this->roles()->where('nombre_rol', 'SuperAdmin')->exists();
        }
        return $this->esSuperAdminCache;
    }

    protected function cargarPermisos(): \Illuminate\Support\Collection
    {
        if (!isset($this->permisosCache)) {
            $this->permisosCache = $this->roles()
                ->with('permisos')
                ->get()
                ->pluck('permisos')
                ->flatten()
                ->pluck('nombre_permiso')
                ->unique();
        }
        return $this->permisosCache;
    }

    public function esMedico(): bool
    {
        if (!isset($this->esMedicoCache)) {
            $this->esMedicoCache = $this->roles()->where('nombre_rol', 'Medico')->exists();
        }
        return $this->esMedicoCache;
    }

    public function medicoProfile(): ?Medico
    {
        return $this->medico;
    }

    public function tieneRelaciones(): bool
    {
        return $this->medico()->exists()
            || $this->paciente()->exists()
            || $this->citasRegistradas()->exists();
    }
}
