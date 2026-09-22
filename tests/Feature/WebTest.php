<?php

namespace Tests\Feature;

use App\Models\Recurso;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitado_es_redirigido_al_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/solicitudes')->assertRedirect('/login');
        $this->get('/recursos')->assertRedirect('/login');
        $this->get('/reportes')->assertRedirect('/login');
    }

    public function test_estudiante_no_puede_acceder_al_panel_administrativo(): void
    {
        $estudiante = User::factory()->create([
            'role' => 'ESTUDIANTE',
        ]);

        $this->actingAs($estudiante)
            ->get('/dashboard')
            ->assertForbidden();
    }

    public function test_estudiante_no_puede_acceder_a_rutas_administrativas(): void
    {
        $estudiante = User::factory()->create(['role' => 'ESTUDIANTE']);

        $this->actingAs($estudiante)
            ->get('/dashboard')
            ->assertForbidden();

        $this->actingAs($estudiante)
            ->get('/solicitudes')
            ->assertForbidden();

        $this->actingAs($estudiante)
            ->get('/recursos')
            ->assertForbidden();

        $this->actingAs($estudiante)
            ->get('/reportes')
            ->assertForbidden();
    }

    public function test_estudiante_no_puede_modificar_estado_prioridad_ni_responsable_por_api(): void
    {
        $estudiante = User::factory()->create(['role' => 'ESTUDIANTE']);
        $admin = User::factory()->create(['role' => 'ADMINISTRATIVO']);
        $solicitud = Solicitud::factory()->create(['estudiante_id' => $estudiante->id]);

        // Intentar cambiar estado siendo estudiante
        $this->actingAs($estudiante)
            ->patchJson("/api/solicitudes/{$solicitud->id}/estado", ['estado' => 'EN_PROCESO'])
            ->assertForbidden();

        // Intentar cambiar prioridad siendo estudiante
        $this->actingAs($estudiante)
            ->patchJson("/api/solicitudes/{$solicitud->id}/prioridad", ['prioridad' => 'ALTA'])
            ->assertForbidden();

        // Intentar asignar responsable siendo estudiante
        $this->actingAs($estudiante)
            ->patchJson("/api/solicitudes/{$solicitud->id}/responsable", ['responsable_id' => $admin->id])
            ->assertForbidden();
    }

    public function test_estudiante_no_puede_consultar_dashboard_ni_reportes_por_api(): void
    {
        $estudiante = User::factory()->create(['role' => 'ESTUDIANTE']);

        $this->actingAs($estudiante)
            ->getJson('/api/dashboard')
            ->assertForbidden();

        $this->actingAs($estudiante)
            ->getJson('/api/reportes/solicitudes')
            ->assertForbidden();

        $this->actingAs($estudiante)
            ->getJson('/api/administrativos')
            ->assertForbidden();
    }

    public function test_estudiante_no_puede_crear_ni_modificar_recursos_por_api(): void
    {
        $estudiante = User::factory()->create(['role' => 'ESTUDIANTE']);
        $recurso = Recurso::create([
            'nombre' => 'Recurso Test',
            'tipo' => 'EQUIPO',
            'estado' => 'DISPONIBLE',
        ]);

        $this->actingAs($estudiante)
            ->postJson('/api/recursos', [
                'nombre' => 'Proyector Aula 101',
                'tipo' => 'EQUIPO',
                'estado' => 'DISPONIBLE',
            ])
            ->assertForbidden();

        $this->actingAs($estudiante)
            ->putJson("/api/recursos/{$recurso->id}", [
                'nombre' => 'Modificado',
            ])
            ->assertForbidden();

        $this->actingAs($estudiante)
            ->deleteJson("/api/recursos/{$recurso->id}")
            ->assertForbidden();
    }

    public function test_login_web_autentica_usuario_administrativo(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@campusconnect.com',
            'password' => '12345678',
            'role' => 'ADMINISTRATIVO',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@campusconnect.com',
            'password' => '12345678',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_web_rechaza_estudiante_en_panel_administrativo(): void
    {
        User::factory()->create([
            'email' => 'estudiante@campusconnect.com',
            'password' => '12345678',
            'role' => 'ESTUDIANTE',
        ]);

        $response = $this->post('/login', [
            'email' => 'estudiante@campusconnect.com',
            'password' => '12345678',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_administrativo_accede_a_vistas_principales(): void
    {
        $admin = User::factory()->create(['role' => 'ADMINISTRATIVO']);
        $solicitud = Solicitud::factory()->create();

        $this->actingAs($admin)->get('/dashboard')->assertOk()->assertSee('Resumen Administrativo');
        $this->actingAs($admin)->get('/solicitudes')->assertOk()->assertSee('Solicitudes Institucionales');
        $this->actingAs($admin)->get("/solicitudes/{$solicitud->id}")->assertOk()->assertSee('Gestión de Solicitud');
        $this->actingAs($admin)->get('/recursos')->assertOk()->assertSee('Recursos Institucionales');
        $this->actingAs($admin)->get('/reportes')->assertOk()->assertSee('Reportes y Estadísticas');
    }

    public function test_endpoint_administrativos_devuelve_usuarios_con_rol_administrativo(): void
    {
        $admin = User::factory()->create(['role' => 'ADMINISTRATIVO', 'name' => 'Admin Uno']);
        User::factory()->create(['role' => 'ESTUDIANTE', 'name' => 'Estudiante Uno']);

        $this->actingAs($admin)
            ->getJson('/api/administrativos')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Admin Uno');
    }

    public function test_logout_cierra_sesion_web(): void
    {
        $admin = User::factory()->create(['role' => 'ADMINISTRATIVO']);

        $this->actingAs($admin)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_login_web_inicia_sesion_con_cookie_sin_bearer_token_y_consume_api_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@campusconnect.com',
            'password' => '12345678',
            'role' => 'ADMINISTRATIVO',
        ]);

        // 1. Simular envío JSON del login desde la Web
        $response = $this->postJson('/login', [
            'email' => 'admin@campusconnect.com',
            'password' => '12345678',
        ]);

        // 2. Verificar que responde exitoso y NO genera token Bearer para Blade
        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.redirect', route('dashboard'))
            ->assertJsonMissing(['token']); // Garantiza que no expone Bearer token a la Web

        // 3. Verificar que el usuario quedó autenticado en la sesión
        $this->assertAuthenticatedAs($admin);

        // 4. Verificar que las peticiones a la API funcionan utilizando la cookie de sesión activa (sin header Authorization)
        $this->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['total', 'por_estado', 'por_tipo', 'por_prioridad']]);
    }
}
