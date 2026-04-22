<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function medico()
    {
        return $this->hasOne(\App\Models\Medico::class, 'id_usuario', 'id');
    }

    public function paciente()
    {
        return $this->hasOne(\App\Models\Paciente::class, 'id_usuario', 'id');
    }

    public function citasRegistradas()
    {
        return $this->hasMany(\App\Models\Cita::class, 'id_usuario_registra', 'id');
    }
}
