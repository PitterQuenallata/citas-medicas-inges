<?php

namespace Tests\Feature;

use App\Http\Middleware\CheckPermiso;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// NUEVO: pruebas Feature para el módulo Especialidades
class EspecialidadTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    /**
     * Crea un usuario autenticado y omite SOLO el middleware de permisos
     * personalizados. El grupo 'web' (ShareErrorsFromSession, etc.) sigue
     * activo para que $errors esté disponible en las vistas.
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

    /** Crea una especialidad mínima válida en la BD. */
    private function crearEspecialidad(array $overrides = []): Especialidad
    {
        return Especialidad::create(array_merge([
            'nombre_especialidad' => 'Cardiología',
            'costo_consulta'      => 150.00,
            'estado'              => 'activo',
        ], $overrides));
    }

    /**
     * Crea un User y un Medico vinculado, necesarios para probar
     * la restricción de eliminación con médicos asociados.
     */
    private function crearMedico(array $overrides = []): Medico
    {
        // Email único para no colisionar con el usuario admin del setUp
        $user = User::create([
            'nombre'   => 'Doctor',
            'apellido' => 'Test',
            'email'    => 'medico_' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'estado'   => 'activo',
        ]);

        return Medico::create(array_merge([
            'id_usuario'            => $user->id,
            'codigo_medico'         => 'MED-' . rand(1000, 9999),
            'nombres'               => 'Juan',
            'apellidos'             => 'Pérez',
            'matricula_profesional' => 'MAT-' . rand(1000, 9999),
            'estado'                => 'activo',
        ], $overrides));
    }

    // ----------------------------------------------------------------
    // Index
    // ----------------------------------------------------------------

    public function test_index_muestra_lista_de_especialidades(): void
    {
        $this->crearEspecialidad(['nombre_especialidad' => 'Neurología']);

        $response = $this->get(route('especialidades.index'));

        $response->assertStatus(200)
                 ->assertSee('Neurología');
    }

    public function test_index_muestra_mensaje_cuando_no_hay_especialidades(): void
    {
        $response = $this->get(route('especialidades.index'));

        $response->assertStatus(200)
                 ->assertSee('No se encontraron especialidades');
    }

    public function test_index_filtra_por_busqueda(): void
    {
        $this->crearEspecialidad(['nombre_especialidad' => 'Cardiología']);
        $this->crearEspecialidad(['nombre_especialidad' => 'Pediatría']);

        $response = $this->get(route('especialidades.index', ['buscar' => 'Cardio']));

        $response->assertStatus(200)
                 ->assertSee('Cardiología')
                 ->assertDontSee('Pediatría');
    }

    // ----------------------------------------------------------------
    // Create / Store
    // ----------------------------------------------------------------

    public function test_muestra_formulario_de_creacion(): void
    {
        $response = $this->get(route('especialidades.create'));

        $response->assertStatus(200);
    }

    public function test_puede_crear_especialidad_con_datos_validos(): void
    {
        $response = $this->post(route('especialidades.store'), [
            'nombre_especialidad' => 'Dermatología',
            'descripcion'         => 'Piel y mucosas',
            'costo_consulta'      => 120.50,
            'estado'              => 'activo',
        ]);

        $response->assertRedirect(route('especialidades.index'))
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('especialidades', [
            'nombre_especialidad' => 'Dermatología',
            'costo_consulta'      => 120.50,
        ]);
    }

    public function test_nombre_es_requerido(): void
    {
        $response = $this->post(route('especialidades.store'), [
            'nombre_especialidad' => '',
            'costo_consulta'      => 100.00,
            'estado'              => 'activo',
        ]);

        $response->assertSessionHasErrors('nombre_especialidad');
    }

    public function test_nombre_debe_ser_unico(): void
    {
        $this->crearEspecialidad(['nombre_especialidad' => 'Cardiología']);

        $response = $this->post(route('especialidades.store'), [
            'nombre_especialidad' => 'Cardiología',
            'costo_consulta'      => 200.00,
            'estado'              => 'activo',
        ]);

        $response->assertSessionHasErrors('nombre_especialidad');
    }

    public function test_costo_consulta_debe_ser_numerico(): void
    {
        $response = $this->post(route('especialidades.store'), [
            'nombre_especialidad' => 'Reumatología',
            'costo_consulta'      => 'no-es-numero',
            'estado'              => 'activo',
        ]);

        $response->assertSessionHasErrors('costo_consulta');
    }

    public function test_costo_consulta_no_puede_ser_negativo(): void
    {
        $response = $this->post(route('especialidades.store'), [
            'nombre_especialidad' => 'Reumatología',
            'costo_consulta'      => -5,
            'estado'              => 'activo',
        ]);

        $response->assertSessionHasErrors('costo_consulta');
    }

    public function test_estado_invalido_es_rechazado(): void
    {
        $response = $this->post(route('especialidades.store'), [
            'nombre_especialidad' => 'Pediatría',
            'costo_consulta'      => 80.00,
            'estado'              => 'pendiente',
        ]);

        $response->assertSessionHasErrors('estado');
    }

    public function test_costo_consulta_es_opcional_y_usa_cero_por_defecto(): void
    {
        $this->post(route('especialidades.store'), [
            'nombre_especialidad' => 'Oncología',
            'estado'              => 'activo',
        ]);

        $this->assertDatabaseHas('especialidades', [
            'nombre_especialidad' => 'Oncología',
            'costo_consulta'      => 0,
        ]);
    }

    // ----------------------------------------------------------------
    // Edit / Update
    // ----------------------------------------------------------------

    public function test_muestra_formulario_de_edicion(): void
    {
        $esp = $this->crearEspecialidad();

        $response = $this->get(route('especialidades.edit', $esp->id_especialidad));

        $response->assertStatus(200)
                 ->assertSee($esp->nombre_especialidad);
    }

    public function test_puede_actualizar_especialidad(): void
    {
        $esp = $this->crearEspecialidad(['nombre_especialidad' => 'Cardiología']);

        $response = $this->put(route('especialidades.update', $esp->id_especialidad), [
            'nombre_especialidad' => 'Cardiología Intervencionista',
            'costo_consulta'      => 300.00,
            'estado'              => 'activo',
        ]);

        $response->assertRedirect(route('especialidades.index'))
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('especialidades', [
            'id_especialidad'     => $esp->id_especialidad,
            'nombre_especialidad' => 'Cardiología Intervencionista',
            'costo_consulta'      => 300.00,
        ]);
    }

    public function test_update_permite_mismo_nombre_para_la_misma_especialidad(): void
    {
        $esp = $this->crearEspecialidad(['nombre_especialidad' => 'Cardiología']);

        $response = $this->put(route('especialidades.update', $esp->id_especialidad), [
            'nombre_especialidad' => 'Cardiología',
            'costo_consulta'      => 200.00,
            'estado'              => 'activo',
        ]);

        $response->assertRedirect(route('especialidades.index'));
        $response->assertSessionDoesntHaveErrors('nombre_especialidad');
    }

    public function test_update_rechaza_nombre_duplicado_de_otra_especialidad(): void
    {
        $this->crearEspecialidad(['nombre_especialidad' => 'Neurología']);
        $esp2 = $this->crearEspecialidad(['nombre_especialidad' => 'Pediatría']);

        $response = $this->put(route('especialidades.update', $esp2->id_especialidad), [
            'nombre_especialidad' => 'Neurología',
            'costo_consulta'      => 100.00,
            'estado'              => 'activo',
        ]);

        $response->assertSessionHasErrors('nombre_especialidad');
    }

    // ----------------------------------------------------------------
    // Destroy
    // ----------------------------------------------------------------

    public function test_puede_eliminar_especialidad_sin_medicos(): void
    {
        $esp = $this->crearEspecialidad();

        $response = $this->delete(route('especialidades.destroy', $esp->id_especialidad));

        $response->assertRedirect(route('especialidades.index'))
                 ->assertSessionHas('success');

        $this->assertDatabaseMissing('especialidades', ['id_especialidad' => $esp->id_especialidad]);
    }

    public function test_no_puede_eliminar_especialidad_con_medicos_asociados(): void
    {
        $esp    = $this->crearEspecialidad();
        $medico = $this->crearMedico();

        // Asociar médico a la especialidad via pivot
        $medico->especialidades()->attach($esp->id_especialidad);

        $response = $this->delete(route('especialidades.destroy', $esp->id_especialidad));

        $response->assertRedirect(route('especialidades.index'))
                 ->assertSessionHas('error');

        // La especialidad sigue en la BD
        $this->assertDatabaseHas('especialidades', ['id_especialidad' => $esp->id_especialidad]);
    }

    // ----------------------------------------------------------------
    // Show
    // ----------------------------------------------------------------

    public function test_muestra_detalle_de_especialidad(): void
    {
        $esp = $this->crearEspecialidad(['nombre_especialidad' => 'Cardiología', 'costo_consulta' => 250.00]);

        $response = $this->get(route('especialidades.show', $esp->id_especialidad));

        $response->assertStatus(200)
                 ->assertSee('Cardiología')
                 ->assertSee('250');
    }
}
