<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Solicitud;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function solicitudes(Request $request)
    {
        $request->validate(['desde' => ['nullable', 'date'], 'hasta' => ['nullable', 'date', 'after_or_equal:desde']]);
        $query = Solicitud::with(['estudiante:id,name', 'responsable:id,name']);
        foreach (['estado', 'tipo', 'prioridad', 'responsable_id'] as $filter) {
            $query->when($request->filled($filter), fn ($q) => $q->where($filter, $request->input($filter)));
        }
        $query->when($request->filled('desde'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('desde')))
            ->when($request->filled('hasta'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('hasta')));
        $solicitudes = $query->latest()->get();
        $cerradas = $solicitudes->whereNotNull('fecha_cierre');

        return response()->json(['success' => true, 'data' => [
            'resumen' => [
                'total' => $solicitudes->count(),
                'pendientes' => $solicitudes->where('estado', 'PENDIENTE')->count(),
                'cerradas' => $solicitudes->where('estado', 'CERRADA')->count(),
                'promedio_horas_atencion' => $cerradas->count() ? round($cerradas->avg(fn ($item) => $item->created_at->diffInMinutes($item->fecha_cierre)) / 60, 2) : null,
            ],
            'solicitudes' => $solicitudes,
        ]]);
    }
}
