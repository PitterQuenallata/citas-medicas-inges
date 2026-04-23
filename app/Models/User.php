<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /**
     * Nombre de la tabla personalizada.
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * Clave primaria personalizada.
     * @var string
     */
    protected $primaryKey = 'id_usuario';

    /**
     * Indica si la clave primaria es autoincremental.
     * @var bool
     */
    public $incrementing = true;

    /**
     * Tipo de la clave primaria.
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indica si el modelo tiene marcas de tiempo.
     * @var bool
     */
    public $timestamps = true;
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'password',
        'estado',
        'id_rol',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
