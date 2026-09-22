<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evidencia;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EvidenciaController extends Controller
{
    public function store(Request $request, Solicitud $solicitud)
    {
        $this->authorize('update', $solicitud);
        $request->validate(['archivo' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240']]);
        $archivo = $request->file('archivo');
        $evidencia = $solicitud->evidencias()->create([
            'nombre_original' => $archivo->getClientOriginalName(),
            'ruta' => $archivo->store('evidencias', 'public'),
            'tipo_archivo' => $archivo->getMimeType(),
        ]);

        return response()->json(['success' => true, 'message' => 'Evidencia adjuntada.', 'data' => $evidencia], 201);
    }

    public function destroy(Request $request, Evidencia $evidencia)
    {
        $this->authorize('update', $evidencia->solicitud);
        Storage::disk('public')->delete($evidencia->ruta);
        $evidencia->delete();

        return response()->json(['success' => true, 'message' => 'Evidencia eliminada.']);
    }
}
