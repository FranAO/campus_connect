<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class SolicitudController extends Controller
{
    public function index()
    {
        return view('solicitudes.index');
    }

    public function show(int $id)
    {
        return view('solicitudes.show', ['solicitudId' => $id]);
    }
}
