<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdministrativo
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->esAdministrativo(), 403, 'Acceso exclusivo para personal administrativo.');

        return $next($request);
    }
}
