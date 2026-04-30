<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckPermiso;
use App\Models\HorarioMedico;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// NUEVO: pruebas Feature para el módulo Horarios Médicos
class HorarioTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    /**
     * Crea un usuario autenticado y omite SOLO el middleware de permisos
     * personalizados. El grupo 'web' sigue activo para $errors en vistas.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'nombre'   => 'Admin',
            'apellido' => 'Test',
            'email'    => 'admin@test.com',
            'password' => bcrypt('password'),
            'estado'   => 'activo',
        ]);

        $this->actingAs($this->user);
        $this->withoutMiddleware(CheckPermiso::class);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /** Crea un User + Medico activo para usar en cada test. */
    private function crearMedico(array $overrides = []): Medico
    {
        // Usar email único para no colisionar con el usuario admin del setUp
        $user = User::create([
            'nombre'   => 'Doctor',
            'apellido' => 'Test',
            'email'    => 'medico_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'estado'   => 'activo',
        ]);

        return Medico::create(array_merge([
            'id_usuario'            => $user->id,
            'codigo_medico'         => 'MED-' . fake()->unique()->numerify('####'),
            'nombres'               => 'Juan',
            'apellidos'             => 'Pérez',
            'matricula_profesional' => 'MAT-' . fake()->unique()->numerify('####'),
            'estado'                => 'activo',
        ], $overrides));
    }

    /** Crea un horario válido en la BD para el médico dado. */
    private function crearHorario(Medico $medico, array $overrides = []): HorarioMedico
    {
        return HorarioMedico::create(array_merge([
            'id_medico'             => $medico->id_medico,
            'dia_semana'            => 1, // Lunes
            'hora_inicio'           => '08:00:00',
            'hora_fin'              => '12:00:00',
            'duracion_cita_minutos' => 30,
            'activo'                => true,
        ], $overrides));
    }

    // ----------------------------------------------------------------
    // Index
    // ----------------------------------------------------------------

    public function test_index_muestra_lista_de_horarios(): void
    {
        $medico = $this->crearMedico();
        $this->crearHorario($medico);

        $response = $this->get(route('horarios.index'));

        $response->assertStatus(200);
    }

    public function test_index_muestra_mensaje_cuando_no_hay_horarios(): void
    {
        $response = $this->get(route('horarios.index'));

        $response->assertStatus(200)
                 ->assertSee('No se encontraron horarios');
    }

    public function test_index_ordena_por_medico_dia_hora(): void
    {
        $medico = $this->crearMedico();
        // Insertar en orden inverso al esperado
        $this->crearHorario($medico, ['dia_semana' => 3, 'hora_inicio' => '14:00:00', 'hora_fin' => '18:00:00']);
        $this->crearHorario($medico, ['dia_semana' => 1, 'hora_inicio' => '08:00:00', 'hora_fin' => '12:00:00']);

        $response = $this->get(route('horarios.index'));

        $response->assertStatus(200);
        // Verificar que ambos registros aparecen
        $this->assertDatabaseCount('horarios_medicos', 2);
    }

    // ----------------------------------------------------------------
    // Create / Store
    // ----------------------------------------------------------------

    public function test_muestra_formulario_de_creacion(): void
    {
        $response = $this->get(route('horarios.create'));

        $response->assertStatus(200);
    }

    public function test_formulario_solo_muestra_medicos_activos(): void
    {
        $medicoActivo   = $this->crearMedico(['estado' => 'activo',   'nombres' => 'Activo']);
        $medicoInactivo = $this->crearMedico(['estado' => 'inactivo', 'nombres' => 'Inactivo']);

        $response = $this->get(route('horarios.create'));

        $response->assertStatus(200)
                 ->assertSee($medicoActivo->nombres)
                 ->assertDontSee($medicoInactivo->nombres);
    }

    public function test_puede_crear_horario_valido(): void
    {
        $medico = $this->crearMedico();

        $response = $this->post(route('horarios.store'), [
            'id_medico'             => $medico->id_medico,
            'dia_semana'            => 1,
            'hora_inicio'           => '08:00',
            'hora_fin'              => '12:00',
            'duracion_cita_minutos' => 30,
            'activo'                => 1,
        ]);

        $response->assertRedirect(route('horarios.index'))
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('horarios_medicos', [
            'id_medico'  => $medico->id_medico,
            'dia_semana' => 1,
        ]);
    }

    public function test_no_puede_crear_horario_con_hora_fin_menor_a_inicio(): void
    {
        $medico = $this->crearMedico();

        $response = $this->post(route('horarios.store'), [
            'id_medico'             => $medico->id_medico,
            'dia_semana'            => 1,
            'hora_inicio'           => '14:00',
            'hora_fin'              => '10:00',
            'duracion_cita_minutos' => 30,
        ]);

        $response->assertSessionHasErrors('hora_fin');
    }

    public function test_no_puede_crear_horario_que_se_solapa_con_existente(): void
    {
        $medico = $this->crearMedico();
        // Horario existente: 08:00 - 12:00 el Lunes
        $this->crearHorario($medico, [
            'dia_semana'  => 1,
            'hora_inicio' => '08:00:00',
            'hora_fin'    => '12:00:00',
        ]);

        // Intentar crear 10:00 - 14:00 (solapa con el anterior)
        $response = $this->post(route('horarios.store'), [
            'id_medico'             => $medico->id_medico,
            'dia_semana'            => 1,
            'hora_inicio'           => '10:00',
            'hora_fin'              => '14:00',
            'duracion_cita_minutos' => 30,
        ]);

        $response->assertSessionHasErrors('hora_inicio');
        $this->assertDatabaseCount('horarios_medicos', 1);
    }

    public function test_puede_crear_horarios_adyacentes_sin_solapamiento(): void
    {
        $medico = $this->crearMedico();
        $this->crearHorario($medico, [
            'dia_semana'  => 1,
            'hora_inicio' => '08:00:00',
            'hora_fin'    => '12:00:00',
        ]);

        // 12:00 - 16:00 (exactamente adyacente, NO debe solaparse)
        $response = $this->post(route('horarios.store'), [
            'id_medico'             => $medico->id_medico,
            'dia_semana'            => 1,
            'hora_inicio'           => '12:00',
            'hora_fin'              => '16:00',
            'duracion_cita_minutos' => 30,
        ]);

        $response->assertRedirect(route('horarios.index'));
        $this->assertDatabaseCount('horarios_medicos', 2);
    }

    public function test_solapamiento_solo_aplica_al_mismo_dia(): void
    {
        $medico = $this->crearMedico();
        $this->crearHorario($medico, [
            'dia_semana'  => 1, // Lunes
            'hora_inicio' => '08:00:00',
            'hora_fin'    => '12:00:00',
        ]);

        // Mismo intervalo horario pero Martes — debe permitirse
        $response = $this->post(route('horarios.store'), [
            'id_medico'             => $medico->id_medico,
            'dia_semana'            => 2, // Martes
            'hora_inicio'           => '08:00',
            'hora_fin'              => '12:00',
            'duracion_cita_minutos' => 30,
        ]);

        $response->assertRedirect(route('horarios.index'));
        $this->assertDatabaseCount('horarios_medicos', 2);
    }

    public function test_medico_es_requerido(): void
    {
        $response = $this->post(route('horarios.store'), [
            'id_medico'             => '',
            'dia_semana'            => 1,
            'hora_inicio'           => '08:00',
            'hora_fin'              => '12:00',
            'duracion_cita_minutos' => 30,
        ]);

        $response->assertSessionHasErrors('id_medico');
    }

    public function test_duracion_minima_es_5_minutos(): void
    {
        $medico = $this->crearMedico();

        $response = $this->post(route('horarios.store'), [
            'id_medico'             => $medico->id_medico,
            'dia_semana'            => 1,
            'hora_inicio'           => '08:00',
            'hora_fin'              => '12:00',
            'duracion_cita_minutos' => 4,
        ]);

        $response->assertSessionHasErrors('duracion_cita_minutos');
    }

    // ----------------------------------------------------------------
    // Edit / Update
    // ----------------------------------------------------------------

    public function test_muestra_formulario_de_edicion(): void
    {
        $medico  = $this->crearMedico();
        $horario = $this->crearHorario($medico);

        $response = $this->get(route('horarios.edit', $horario->id_horario));

        $response->assertStatus(200);
    }

    public function test_puede_actualizar_horario_sin_solapamiento(): void
    {
        $medico  = $this->crearMedico();
        $horario = $this->crearHorario($medico, [
            'dia_semana'  => 1,
            'hora_inicio' => '08:00:00',
            'hora_fin'    => '12:00:00',
        ]);

        $response = $this->put(route('horarios.update', $horario->id_horario), [
            'id_medico'             => $medico->id_medico,
            'dia_semana'            => 1,
            'hora_inicio'           => '09:00',
            'hora_fin'              => '13:00',
            'duracion_cita_minutos' => 30,
        ]);

        $response->assertRedirect(route('horarios.index'))
                 ->assertSessionHas('success');
    }

    public function test_update_excluye_el_horario_actual_de_la_validacion(): void
    {
        $medico  = $this->crearMedico();
        $horario = $this->crearHorario($medico, [
            'dia_semana'  => 1,
            'hora_inicio' => '08:00:00',
            'hora_fin'    => '12:00:00',
        ]);

        // Actualizar con los mismos datos (no debería detectar solapamiento consigo mismo)
        $response = $this->put(route('horarios.update', $horario->id_horario), [
            'id_medico'             => $medico->id_medico,
            'dia_semana'            => 1,
            'hora_inicio'           => '08:00',
            'hora_fin'              => '12:00',
            'duracion_cita_minutos' => 30,
        ]);

        $response->assertRedirect(route('horarios.index'));
        $response->assertSessionDoesntHaveErrors('hora_inicio');
    }

    public function test_update_detecta_solapamiento_con_otro_horario(): void
    {
        $medico = $this->crearMedico();
        // Horario existente: 08:00 - 12:00
        $this->crearHorario($medico, [
            'dia_semana'  => 1,
            'hora_inicio' => '08:00:00',
            'hora_fin'    => '12:00:00',
        ]);
        // Horario a editar: 14:00 - 18:00
        $horario = $this->crearHorario($medico, [
            'dia_semana'  => 1,
            'hora_inicio' => '14:00:00',
            'hora_fin'    => '18:00:00',
        ]);

        // Intentar moverlo a 10:00 - 16:00 (solapa con el primero)
        $response = $this->put(route('horarios.update', $horario->id_horario), [
            'id_medico'             => $medico->id_medico,
            'dia_semana'            => 1,
            'hora_inicio'           => '10:00',
            'hora_fin'              => '16:00',
            'duracion_cita_minutos' => 30,
        ]);

        $response->assertSessionHasErrors('hora_inicio');
    }

    // ----------------------------------------------------------------
    // Destroy
    // ----------------------------------------------------------------

    public function test_puede_eliminar_horario(): void
    {
        $medico  = $this->crearMedico();
        $horario = $this->crearHorario($medico);

        $response = $this->delete(route('horarios.destroy', $horario->id_horario));

        $response->assertRedirect(route('horarios.index'))
                 ->assertSessionHas('success');

        $this->assertDatabaseMissing('horarios_medicos', ['id_horario' => $horario->id_horario]);
    }
}
