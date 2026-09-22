<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SolicitudFactory extends Factory
{
    public function definition(): array
    {
        return [
            'estudiante_id' => User::factory()->state(['role' => 'ESTUDIANTE']),
            'titulo' => fake()->sentence(4),
            'descripcion' => fake()->paragraph(),
            'tipo' => 'MANTENIMIENTO',
            'prioridad' => 'MEDIA',
            'estado' => 'PENDIENTE',
            'ubicacion' => fake()->address(),
        ];
    }
}
