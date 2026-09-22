<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('users');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('tipo');
            $table->string('prioridad')->default('MEDIA');
            $table->string('estado')->default('PENDIENTE');
            $table->string('ubicacion')->nullable();
            $table->timestamp('fecha_cierre')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes')->cascadeOnDelete();
            $table->string('nombre_original');
            $table->string('ruta');
            $table->string('tipo_archivo');
            $table->timestamps();
        });

        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->text('contenido');
            $table->timestamps();
        });

        Schema::create('historial_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_id')->constrained('solicitudes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->string('accion');
            $table->string('estado_anterior')->nullable();
            $table->string('estado_nuevo')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('recursos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo');
            $table->text('descripcion')->nullable();
            $table->string('estado')->default('DISPONIBLE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recursos');
        Schema::dropIfExists('historial_solicitudes');
        Schema::dropIfExists('comentarios');
        Schema::dropIfExists('evidencias');
        Schema::dropIfExists('solicitudes');
    }
};
