<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Vérifie si l'utilisateur a l'un des rôles spécifiés
            if (!in_array($user->role, $roles)) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    }
}
