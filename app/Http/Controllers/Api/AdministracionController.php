<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HistorialSolicitud;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdministracionController extends Controller
{
    public function estado(Request $request, Solicitud $solicitud)
    {
        $data = $request->validate(['estado' => ['required', Rule::in(['PENDIENTE', 'ASIGNADA', 'EN_PROCESO', 'RESUELTA', 'CERRADA'])]]);
        $anterior = $solicitud->estado;
        $solicitud->update(['estado' => $data['estado'], 'fecha_cierre' => $data['estado'] === 'CERRADA' ? now() : null]);
        $this->registrar($request, $solicitud, 'CAMBIO_ESTADO', $anterior, $data['estado']);

        return response()->json(['success' => true, 'message' => 'Estado actualizado.', 'data' => $solicitud]);
    }

    public function prioridad(Request $request, Solicitud $solicitud)
    {
        $data = $request->validate(['prioridad' => ['required', Rule::in(['BAJA', 'MEDIA', 'ALTA', 'URGENTE'])]]);
        $anterior = $solicitud->prioridad;
        $solicitud->update($data);
        $this->registrar($request, $solicitud, 'CAMBIO_PRIORIDAD', $anterior, $data['prioridad']);

        return response()->json(['success' => true, 'message' => 'Prioridad actualizada.', 'data' => $solicitud]);
    }

    public function responsable(Request $request, Solicitud $solicitud)
    {
        $data = $request->validate(['responsable_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'ADMINISTRATIVO')]]);
        $anterior = $solicitud->responsable?->name;
        $nuevo = User::findOrFail($data['responsable_id']);
        $solicitud->update(['responsable_id' => $nuevo->id, 'estado' => $solicitud->estado === 'PENDIENTE' ? 'ASIGNADA' : $solicitud->estado]);
        $this->registrar($request, $solicitud, 'ASIGNACION_RESPONSABLE', null, null, ($anterior ?? 'Sin asignar').' - '.$nuevo->name);

        return response()->json(['success' => true, 'message' => 'Responsable asignado.', 'data' => $solicitud->load('responsable:id,name,email')]);
    }

    public function administrativos()
    {
        $users = User::where('role', 'ADMINISTRATIVO')->select('id', 'name', 'email')->get();

        return response()->json(['success' => true, 'data' => $users]);
    }

    private function registrar(Request $request, Solicitud $solicitud, string $accion, ?string $anterior = null, ?string $nuevo = null, ?string $descripcion = null): void
    {
        HistorialSolicitud::create(['solicitud_id' => $solicitud->id, 'user_id' => $request->user()->id, 'accion' => $accion, 'estado_anterior' => $anterior, 'estado_nuevo' => $nuevo, 'descripcion' => $descripcion]);
    }
}
