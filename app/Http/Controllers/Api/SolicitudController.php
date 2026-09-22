<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HistorialSolicitud;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SolicitudController extends Controller
{
    private const TIPOS = ['MANTENIMIENTO', 'SOPORTE_TECNOLOGICO', 'INFRAESTRUCTURA', 'EQUIPAMIENTO', 'OTRO'];

    public function index(Request $request)
    {
        $query = Solicitud::with(['estudiante:id,name,email', 'responsable:id,name,email']);
        if (! $request->user()->esAdministrativo()) {
            $query->where('estudiante_id', $request->user()->id);
        }

        foreach (['estado', 'tipo', 'prioridad', 'responsable_id'] as $filter) {
            $query->when($request->filled($filter), fn ($q) => $q->where($filter, $request->input($filter)));
        }

        return response()->json(['success' => true, 'data' => $query->latest()->paginate(min($request->integer('per_page', 15), 100))]);
    }

    public function store(Request $request)
    {
        abort_if($request->user()->esAdministrativo(), 403, 'Solo estudiantes pueden crear solicitudes.');
        $data = $request->validate([
            'titulo' => ['required', 'string', 'max:150'],
            'descripcion' => ['required', 'string', 'max:5000'],
            'tipo' => ['required', Rule::in(self::TIPOS)],
            'ubicacion' => ['nullable', 'string', 'max:255'],
        ]);
        $solicitud = $request->user()->solicitudes()->create($data + ['prioridad' => 'MEDIA', 'estado' => 'PENDIENTE']);
        HistorialSolicitud::create(['solicitud_id' => $solicitud->id, 'user_id' => $request->user()->id, 'accion' => 'SOLICITUD_CREADA', 'descripcion' => 'Solicitud registrada']);

        return response()->json(['success' => true, 'message' => 'Solicitud creada correctamente.', 'data' => $solicitud], 201);
    }

    public function show(Request $request, Solicitud $solicitud)
    {
        $this->authorize('view', $solicitud);

        return response()->json(['success' => true, 'data' => $solicitud->load(['estudiante:id,name,email', 'responsable:id,name,email', 'evidencias', 'comentarios.usuario:id,name', 'historial.usuario:id,name'])]);
    }

    public function update(Request $request, Solicitud $solicitud)
    {
        $this->authorize('update', $solicitud);
        $data = $request->validate([
            'titulo' => ['sometimes', 'required', 'string', 'max:150'],
            'descripcion' => ['sometimes', 'required', 'string', 'max:5000'],
            'tipo' => ['sometimes', Rule::in(self::TIPOS)],
            'ubicacion' => ['nullable', 'string', 'max:255'],
        ]);
        $solicitud->update($data);

        return response()->json(['success' => true, 'message' => 'Solicitud actualizada.', 'data' => $solicitud->fresh()]);
    }

    public function destroy(Request $request, Solicitud $solicitud)
    {
        $this->authorize('delete', $solicitud);
        $solicitud->delete();

        return response()->json(['success' => true, 'message' => 'Solicitud eliminada.']);
    }

    public function historial(Request $request, Solicitud $solicitud)
    {
        $this->authorize('view', $solicitud);

        return response()->json(['success' => true, 'data' => $solicitud->historial()->with('usuario:id,name')->get()]);
    }
}
