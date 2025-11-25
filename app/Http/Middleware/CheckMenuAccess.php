<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $menuKey
     */
    public function handle(Request $request, Closure $next, string $menuKey): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if user has access to this menu
        if (!auth()->user()->hasMenuAccess($menuKey)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
