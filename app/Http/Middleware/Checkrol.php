<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Checkrol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $rol): Response
    {
        if(!Auth::check() || strtolower(Auth::user()->rol) !== strtolower($rol)) {
            abort(403, 'No tienes permiso de acceder a esta pagina');
        }
        return $next($request);
    }
}
