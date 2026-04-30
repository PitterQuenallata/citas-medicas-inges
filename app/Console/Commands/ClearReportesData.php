<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

#[Signature('db:clear-reportes')]
#[Description('Limpia los datos de prueba de pagos, consultas médicas y citas generados por el ReportesSeeder')]
class ClearReportesData extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->confirm('¿Estás seguro de que deseas eliminar TODOS los pagos, consultas y citas del sistema?')) {
            $this->info('Iniciando la eliminación de datos de reportes...');

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            DB::table('pagos')->truncate();
            $this->info('✓ Pagos eliminados.');

            DB::table('consultas_medicas')->truncate();
            $this->info('✓ Consultas médicas eliminadas.');

            DB::table('citas')->truncate();
            $this->info('✓ Citas eliminadas.');

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('¡Datos de reportes eliminados con éxito!');
        } else {
            $this->info('Operación cancelada.');
        }
    }
}
