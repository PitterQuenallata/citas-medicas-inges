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
            'Pediatría'              => 60.00,
            'Ginecología'            => 80.00,
            'Cardiología'            => 100.00,
            'Dermatología'           => 70.00,
            'Traumatología'          => 90.00,
            'Oftalmología'           => 75.00,
            'Otorrinolaringología'   => 80.00,
            'Neurología'             => 120.00,
            'Urología'               => 85.00,
        ];

        foreach ($costos as $nombre => $costo) {
            Especialidad::where('nombre_especialidad', $nombre)
                ->update(['costo_consulta' => $costo]);
        }
    }
}
