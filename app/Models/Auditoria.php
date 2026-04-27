<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Auditoria extends Model
{
    protected $table = 'auditorias';
    protected $primaryKey = 'id_auditoria';

    protected $fillable = [
        'id_usuario',
        'accion',
        'tabla',
        'registro_id',
        'datos_anteriores',
        'datos_nuevos',
        'ip',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public static function registrar(string $accion, string $tabla, $registroId = null, $datosAnteriores = null, $datosNuevos = null): self
    {
        return self::create([
            'id_usuario'       => Auth::id(),
            'accion'           => $accion,
            'tabla'            => $tabla,
            'registro_id'      => $registroId,
            'datos_anteriores' => $datosAnteriores ? json_encode($datosAnteriores) : null,
            'datos_nuevos'     => $datosNuevos ? json_encode($datosNuevos) : null,
            'ip'               => Request::ip(),
        ]);
    }
}
