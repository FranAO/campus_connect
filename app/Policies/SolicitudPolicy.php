<?php

namespace App\Policies;

use App\Models\Solicitud;
use App\Models\User;

class SolicitudPolicy
{
    public function view(User $user, Solicitud $solicitud): bool
    {
        return $user->esAdministrativo() || $solicitud->estudiante_id === $user->id;
    }

    public function update(User $user, Solicitud $solicitud): bool
    {
        return $user->esAdministrativo() || $solicitud->estudiante_id === $user->id;
    }

    public function delete(User $user, Solicitud $solicitud): bool
    {
        return $this->update($user, $solicitud);
    }
}
