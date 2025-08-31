<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        \Log::info('RoleMiddleware triggered for in middleware: ' . optional(auth()->user())->id . ' with role: ' . optional(auth()->user())->role);

        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!in_array($request->user()->role, $roles)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
