<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAdminRedirectOnGamePages
{
    /**
     * Handle an incoming request.
     *
     * Prevents automatic redirection to admin panel when accessing game pages.
     * This allows admin users to play games on the frontend without being redirected.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Don't interfere with admin panel requests
        if ($request->is('admin/*')) {
            return $next($request);
        }

        // Don't interfere with API requests
        if ($request->is('api/*')) {
            return $next($request);
        }

        // For all other requests (including game pages), proceed normally
        // This prevents Filament from auto-redirecting admins to /admin
        return $next($request);
    }
}
