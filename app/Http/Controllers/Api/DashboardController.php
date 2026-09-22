<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return response()->json(['success' => true, 'data' => [
            'total' => Solicitud::count(),
            'por_estado' => Solicitud::query()->selectRaw('estado, count(*) as total')->groupBy('estado')->pluck('total', 'estado'),
            'por_tipo' => Solicitud::query()->selectRaw('tipo, count(*) as total')->groupBy('tipo')->pluck('total', 'tipo'),
            'por_prioridad' => Solicitud::query()->selectRaw('prioridad, count(*) as total')->groupBy('prioridad')->pluck('total', 'prioridad'),
        ]]);
    }
}
