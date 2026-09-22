<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recurso;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecursoController extends Controller
{
    public function index(Request $request)
    {
        $query = Recurso::query();
        $query->when($request->filled('tipo'), fn ($q) => $q->where('tipo', $request->input('tipo')));
        $query->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->input('estado')));
        $query->when($request->filled('buscar'), function ($q) use ($request) {
            $term = '%'.$request->input('buscar').'%';
            $q->where(fn ($sub) => $sub->where('nombre', 'like', $term)->orWhere('descripcion', 'like', $term));
        });

        return response()->json(['success' => true, 'data' => $query->latest()->paginate(20)]);
    }

    public function store(Request $request)
    {
        $recurso = Recurso::create($this->validar($request));

        return response()->json(['success' => true, 'message' => 'Recurso creado.', 'data' => $recurso], 201);
    }

    public function show(Recurso $recurso)
    {
        return response()->json(['success' => true, 'data' => $recurso]);
    }

    public function update(Request $request, Recurso $recurso)
    {
        $recurso->update($this->validar($request, true));

        return response()->json(['success' => true, 'message' => 'Recurso actualizado.', 'data' => $recurso]);
    }

    public function destroy(Recurso $recurso)
    {
        $recurso->delete();

        return response()->json(['success' => true, 'message' => 'Recurso eliminado.']);
    }

    private function validar(Request $request, bool $parcial = false): array
    {
        $required = $parcial ? 'sometimes' : 'required';

        return $request->validate([
            'nombre' => [$required, 'string', 'max:150'],
            'tipo' => [$required, 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:2000'],
            'estado' => [$required, Rule::in(['DISPONIBLE', 'EN_USO', 'MANTENIMIENTO', 'NO_DISPONIBLE'])],
        ]);
    }
}
