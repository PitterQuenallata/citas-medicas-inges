<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cita;
use App\Models\ConsultaMedica;
use App\Models\Pago;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;

class ReportesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        
        $medicos = Medico::all();
        $pacientes = Paciente::all();
        $usuarioRegistra = User::first();

        if ($medicos->isEmpty() || $pacientes->isEmpty() || !$usuarioRegistra) {
            $this->command->warn('Se requieren médicos, pacientes y un usuario administrador para generar citas. Ejecute php artisan db:seed primero.');
            return;
        }

        $metodosPago = ['qr', 'efectivo', 'transferencia'];
        $cantidadCitas = 100;
        
        $this->command->info("Generando {$cantidadCitas} citas para reportes...");

        for ($i = 0; $i < $cantidadCitas; $i++) {
            $medico = $medicos->random();
            $paciente = $pacientes->random();
            
            // Random date within the last 90 days or up to 7 days in the future
            $fechaCita = Carbon::today()->subDays(rand(-7, 90));
            $horaInicio = Carbon::createFromTime(rand(8, 17), rand(0, 1) * 30, 0);
            $horaFin = (clone $horaInicio)->addMinutes(30);
            
            $estadoAleatorio = rand(1, 100);
            if ($fechaCita->isFuture()) {
                $estado = $faker->randomElement(['pendiente', 'confirmada', 'reprogramada']);
            } else {
                if ($estadoAleatorio <= 60) $estado = 'atendida';
                elseif ($estadoAleatorio <= 80) $estado = 'cancelada';
                elseif ($estadoAleatorio <= 90) $estado = 'no_asistio';
                else $estado = $faker->randomElement(['pendiente', 'reprogramada']);
            }
            
            $cita = Cita::create([
                'codigo_cita' => 'CIT-' . rand(1000, 9999) . '-' . str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT) . rand(0,9),
                'id_paciente' => $paciente->id_paciente,
                'id_medico' => $medico->id_medico,
                'id_usuario_registra' => $usuarioRegistra->id,
                'fecha_cita' => $fechaCita->format('Y-m-d'),
                'hora_inicio' => $horaInicio->format('H:i:s'),
                'hora_fin' => $horaFin->format('H:i:s'),
                'motivo_consulta' => $faker->sentence(6),
                'estado_cita' => $estado,
                'observaciones' => $faker->optional(0.3)->sentence(),
                'fecha_cancelacion' => $estado === 'cancelada' ? $fechaCita->copy()->subDays(rand(1, 3)) : null,
                'motivo_cancelacion' => $estado === 'cancelada' ? $faker->sentence(4) : null,
            ]);

            // Si fue atendida, generar consulta médica
            if ($estado === 'atendida') {
                ConsultaMedica::create([
                    'id_cita' => $cita->id_cita,
                    'id_paciente' => $paciente->id_paciente,
                    'id_medico' => $medico->id_medico,
                    'fecha_consulta' => $fechaCita->copy()->setTime($horaInicio->hour, $horaInicio->minute),
                    'motivo_consulta' => $cita->motivo_consulta,
                    'sintomas' => $faker->paragraph(2),
                    'diagnostico' => $faker->paragraph(1),
                    'tratamiento' => $faker->paragraph(2),
                    'receta' => $faker->optional(0.7)->paragraph(2),
                    'peso' => $faker->randomFloat(2, 50, 100),
                    'talla' => $faker->randomFloat(2, 140, 190),
                    'presion_arterial' => rand(100, 140) . '/' . rand(60, 90),
                    'temperatura' => $faker->randomFloat(1, 36.0, 39.5),
                ]);
            }

            // Generar pago para algunas citas
            if (in_array($estado, ['atendida', 'confirmada']) || rand(1, 10) > 7) {
                $estadoPago = $estado === 'cancelada' ? 'anulado' : ($estado === 'pendiente' ? 'pendiente' : 'pagado');
                $metodo = $faker->randomElement($metodosPago);
                $monto = $medico->especialidades->first()?->pivot->costo_consulta ?? rand(150, 300);
                
                Pago::create([
                    'id_cita' => $cita->id_cita,
                    'codigo_pago' => 'PAG-' . rand(1000, 9999) . '-' . str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT) . rand(0,9),
                    'monto' => $monto,
                    'metodo_pago' => $metodo,
                    'estado_pago' => $estadoPago,
                    'referencia_externa' => in_array($metodo, ['qr', 'transferencia']) ? rand(1000000, 9999999) : null,
                    'fecha_pago' => $estadoPago === 'pagado' ? $fechaCita->copy()->subMinutes(rand(10, 120)) : null,
                    'id_usuario_registra' => $usuarioRegistra->id,
                    'observaciones' => $faker->optional(0.2)->sentence(),
                ]);
            }
        }
        
        $this->command->info('ReportesSeeder finalizado con éxito.');
    }
}
