<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class CheckRoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        $roles = [
            'administrador' => 1,
            // Añade más roles según sea necesario
        ];

        $userId = $request->route('userId');

        if (is_numeric($userId) && $user = User::find($userId)) {

        

        if ($user && $user->rol_id == $roles[$role]) {
            return $next($request);
        }
    }

        return redirect('/')->with('error', 'Acceso no autorizado.');
    }
}
