<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->route('userId');

        // Verifica si el userId es un número y obtén el usuario correspondiente
        if (is_numeric($userId) && $user = User::find($userId)) {
            // Verifica si el usuario tiene el rol_id correcto
            if ($user->rol_id == 1) {
                
                
                return $next($request);
            }
        }

        // Si no cumple con las condiciones, redirige a donde desees
        return redirect('/')->with('error', 'Acceso no autorizado.');
    }
}

