<?php

namespace Tests\Feature;

use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_devuelve_token(): void
    {
        User::factory()->create(['email' => 'estudiante@test.com', 'password' => 'secreto123']);
        $this->postJson('/api/login', ['email' => 'estudiante@test.com', 'password' => 'secreto123'])
            ->assertOk()->assertJsonPath('success', true)->assertJsonStructure(['data' => ['token']]);
    }

    public function test_estudiante_crea_solicitud_con_valores_iniciales(): void
    {
        $user = User::factory()->create(['role' => 'ESTUDIANTE']);
        Sanctum::actingAs($user);
        $this->postJson('/api/solicitudes', ['titulo' => 'Proyector dañado', 'descripcion' => 'No enciende', 'tipo' => 'EQUIPAMIENTO'])
            ->assertCreated()->assertJsonPath('data.estado', 'PENDIENTE')->assertJsonPath('data.prioridad', 'MEDIA');
        $this->assertDatabaseHas('historial_solicitudes', ['accion' => 'SOLICITUD_CREADA']);
    }

    public function test_estudiante_solo_lista_sus_solicitudes(): void
    {
        $user = User::factory()->create(['role' => 'ESTUDIANTE']);
        $otro = User::factory()->create(['role' => 'ESTUDIANTE']);
        Solicitud::factory()->create(['estudiante_id' => $user->id]);
        Solicitud::factory()->create(['estudiante_id' => $otro->id]);
        Sanctum::actingAs($user);
        $this->getJson('/api/solicitudes')->assertOk()->assertJsonCount(1, 'data.data');
    }

    public function test_estudiante_no_puede_cambiar_estado(): void
    {
        $user = User::factory()->create(['role' => 'ESTUDIANTE']);
        $solicitud = Solicitud::factory()->create(['estudiante_id' => $user->id]);
        Sanctum::actingAs($user);
        $this->patchJson("/api/solicitudes/{$solicitud->id}/estado", ['estado' => 'EN_PROCESO'])->assertForbidden();
    }

    public function test_administrativo_cambia_estado_y_genera_historial(): void
    {
        $admin = User::factory()->create(['role' => 'ADMINISTRATIVO']);
        $solicitud = Solicitud::factory()->create();
        Sanctum::actingAs($admin);
        $this->patchJson("/api/solicitudes/{$solicitud->id}/estado", ['estado' => 'EN_PROCESO'])->assertOk();
        $this->assertDatabaseHas('historial_solicitudes', ['solicitud_id' => $solicitud->id, 'accion' => 'CAMBIO_ESTADO', 'estado_nuevo' => 'EN_PROCESO']);
    }

    public function test_estudiante_adjunta_evidencia(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['role' => 'ESTUDIANTE']);
        $solicitud = Solicitud::factory()->create(['estudiante_id' => $user->id]);
        Sanctum::actingAs($user);
        $this->postJson("/api/solicitudes/{$solicitud->id}/evidencias", ['archivo' => UploadedFile::fake()->create('evidencia.pdf', 100, 'application/pdf')])->assertCreated();
        $this->assertDatabaseCount('evidencias', 1);
    }

    public function test_comentarios_son_visibles_para_dueño(): void
    {
        $user = User::factory()->create(['role' => 'ESTUDIANTE']);
        $admin = User::factory()->create(['role' => 'ADMINISTRATIVO']);
        $solicitud = Solicitud::factory()->create(['estudiante_id' => $user->id]);
        $solicitud->comentarios()->create(['user_id' => $admin->id, 'contenido' => 'En revisión']);
        Sanctum::actingAs($user);
        $this->getJson("/api/solicitudes/{$solicitud->id}/comentarios")->assertOk()->assertJsonPath('data.0.contenido', 'En revisión');
    }

    public function test_dashboard_es_exclusivo_para_administrativos(): void
    {
        Sanctum::actingAs(User::factory()->create(['role' => 'ADMINISTRATIVO']));
        Solicitud::factory()->count(3)->create();
        $this->getJson('/api/dashboard')->assertOk()->assertJsonPath('data.total', 3);
    }
}
