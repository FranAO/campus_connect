<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    public function index(Request $request, Solicitud $solicitud)
    {
        $this->authorize('view', $solicitud);

        return response()->json(['success' => true, 'data' => $solicitud->comentarios()->with('usuario:id,name')->latest()->get()]);
    }

    public function store(Request $request, Solicitud $solicitud)
    {
        abort_unless($request->user()->esAdministrativo(), 403);
        $data = $request->validate(['contenido' => ['required', 'string', 'max:2000']]);
        $comentario = $solicitud->comentarios()->create($data + ['user_id' => $request->user()->id]);

        return response()->json(['success' => true, 'message' => 'Comentario agregado.', 'data' => $comentario->load('usuario:id,name')], 201);
    }
}
