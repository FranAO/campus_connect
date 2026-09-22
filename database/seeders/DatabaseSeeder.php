<?php

namespace Database\Seeders;

use App\Models\Comentario;
use App\Models\HistorialSolicitud;
use App\Models\Recurso;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create(['name' => 'Administrador Campus', 'email' => 'admin@campusconnect.com', 'password' => '12345678', 'role' => 'ADMINISTRATIVO']);
        $estudiantes = collect([
            ['name' => 'Estudiante Demo', 'email' => 'estudiante@campusconnect.com'],
            ['name' => 'Ana Pérez', 'email' => 'ana@campusconnect.com'],
            ['name' => 'Luis Vargas', 'email' => 'luis@campusconnect.com'],
            ['name' => 'María López', 'email' => 'maria@campusconnect.com'],
        ])->map(fn ($data) => User::create($data + ['password' => '12345678', 'role' => 'ESTUDIANTE']));

        $tipos = ['MANTENIMIENTO', 'SOPORTE_TECNOLOGICO', 'INFRAESTRUCTURA', 'EQUIPAMIENTO', 'OTRO'];
        $estados = ['PENDIENTE', 'ASIGNADA', 'EN_PROCESO', 'RESUELTA', 'CERRADA'];
        $prioridades = ['BAJA', 'MEDIA', 'ALTA', 'URGENTE'];

        foreach (range(1, 20) as $i) {
            $estado = $estados[$i % count($estados)];
            $solicitud = Solicitud::create([
                'estudiante_id' => $estudiantes[$i % $estudiantes->count()]->id,
                'responsable_id' => $estado === 'PENDIENTE' ? null : $admin->id,
                'titulo' => "Solicitud de prueba {$i}",
                'descripcion' => "Descripción de solicitud de prueba {$i}",
                'tipo' => $tipos[$i % count($tipos)],
                'prioridad' => $prioridades[$i % count($prioridades)],
                'estado' => $estado,
                'ubicacion' => 'Campus universitario',
                'fecha_cierre' => $estado === 'CERRADA' ? now() : null,
            ]);
            HistorialSolicitud::create(['solicitud_id' => $solicitud->id, 'user_id' => $solicitud->estudiante_id, 'accion' => 'SOLICITUD_CREADA', 'descripcion' => 'Solicitud registrada']);
            if ($i <= 10) {
                Comentario::create(['solicitud_id' => $solicitud->id, 'user_id' => $admin->id, 'contenido' => "Seguimiento administrativo {$i}"]);
            }
        }

        foreach (range(1, 10) as $i) {
            Recurso::create(['nombre' => "Recurso {$i}", 'tipo' => $i % 2 ? 'EQUIPO' : 'ESPACIO', 'descripcion' => "Recurso institucional {$i}", 'estado' => 'DISPONIBLE']);
        }
    }
}
