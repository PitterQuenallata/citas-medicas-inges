<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'password',
        'estado',
        'id_rol',
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
        return $this->hasOne(Medico::class, 'id_usuario', 'id_usuario');
    }

    public function paciente(): HasOne
    {
        return $this->hasOne(Paciente::class, 'id_usuario', 'id_usuario');
    }

    public function citasRegistradas(): HasMany
    {
        return $this->hasMany(Cita::class, 'id_usuario_registra', 'id_usuario');
    }
}
