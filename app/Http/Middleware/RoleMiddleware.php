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
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Check if user is logged in and has the matching role
        if (! $request->user() || $request->user()->role !== $role) {
            // If not, abort with 403 Forbidden
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}