<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class RecursoController extends Controller
{
    public function index()
    {
        return view('recursos.index');
    }
}
