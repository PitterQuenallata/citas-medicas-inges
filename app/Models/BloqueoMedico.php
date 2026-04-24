<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloqueoMedico extends Model
{
    use HasFactory;

    protected $table = 'bloqueos_medicos';

    protected $primaryKey = 'id_bloqueo';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'id_medico',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'motivo',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'id_medico', 'id_medico');
    }
}
