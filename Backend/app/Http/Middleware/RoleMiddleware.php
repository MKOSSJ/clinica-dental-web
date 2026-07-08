<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return response()->json([
                'status' => false,
                'message' => 'No autenticado',
                'data' => [],
            ], 401);
        }

        if (! in_array($request->user()->role, $roles, true)) {
            return response()->json([
                'status' => false,
                'message' => 'No autorizado',
                'data' => [],
            ], 403);
        }

        return $next($request);
    }
}
