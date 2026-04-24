<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $table = 'usuarios';

    protected $primaryKey = 'id_usuario';

    public $incrementing = true;

    protected $keyType = 'int';

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
    ];

    public function getRememberTokenName()
    {
        return null;
    }

    protected function casts(): array
    {
        return [
            'ultimo_login' => 'datetime',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usuario_rol', 'id_usuario', 'id_rol');
    }

    public function medico()
    {
        return $this->hasOne(Medico::class, 'id_usuario', 'id_usuario');
    }

    public function paciente()
    {
        return $this->hasOne(Paciente::class, 'id_usuario', 'id_usuario');
    }
}
