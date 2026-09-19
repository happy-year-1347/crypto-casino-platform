<?php

namespace App\Http\Middleware;

use App\Support\Locales;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetDefaultLanguage
{
    /**
     * Picks the language for server messages (validation, API errors) and the
     * page shell. The admin panel always stays in English.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if ($request->is('admin', 'admin/*', 'livewire/*', 'filament/*')) {
            app()->setLocale('en');
            return $next($request);
        }

        $user = null;
        try {
            if ($request->bearerToken() && auth('api')->check()) {
                $user = auth('api')->user();
            }
        } catch (\Throwable $e) {
            // invalid/expired token: the route's own auth decides what to do
        }

        app()->setLocale(Locales::resolve($request, $user));

        return $next($request);
    }
}
