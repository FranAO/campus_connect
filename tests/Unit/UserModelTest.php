<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserModelTest extends TestCase
{
    public function test_usuario_con_rol_administrativo_es_administrativo(): void
    {
        $user = new User(['role' => 'ADMINISTRATIVO']);
        $this->assertTrue($user->esAdministrativo());
    }

    public function test_usuario_estudiante_no_es_administrativo(): void
    {
        $user = new User(['role' => 'ESTUDIANTE']);
        $this->assertFalse($user->esAdministrativo());
    }
}
