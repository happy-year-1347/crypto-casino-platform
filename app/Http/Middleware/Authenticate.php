<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        /// There is no server-side route named "login": the sign-in screen is a
        /// page inside the Vue app. Asking for route('login') threw, so every
        /// signed-out visit to a guarded address became a 500 and an entry in
        /// the error log instead of a redirect to the sign-in page.
        return $request->expectsJson() ? null : url('/login');
    }
}
