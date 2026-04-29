<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;

class CostoEspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $costos = [
            'Medicina General'       => 50.00,
            'Pediatria'              => 60.00,
            'Ginecologia'            => 80.00,
            'Cardiologia'            => 100.00,
            'Dermatologia'           => 70.00,
            'Traumatologia'          => 90.00,
            'Oftalmologia'           => 75.00,
            'Otorrinolaringologia'   => 80.00,
            'Neurologia'             => 120.00,
            'Urologia'               => 85.00,
        ];

        foreach ($costos as $nombre => $costo) {
            Especialidad::where('nombre_especialidad', $nombre)
                ->update(['costo_consulta' => $costo]);
        }
    }
}
