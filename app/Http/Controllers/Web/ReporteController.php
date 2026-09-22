<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class ReporteController extends Controller
{
    public function index()
    {
        return view('reportes.index');
    }
}
