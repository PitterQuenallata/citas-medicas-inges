<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;

    protected $table = 'permisos';

    protected $primaryKey = 'id_permiso';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nombre_permiso',
        'descripcion',
        'modulo',
    ];

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_permiso', 'id_permiso', 'id_rol');
    }
}
