<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthWithRedirect
{
    /**
     * Handle an incoming request.
     * Redirect to login with the current URL as intended parameter
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Pour les requêtes GET, passer l'URL courante comme paramètre intended
            if ($request->method() === 'GET' && !$request->ajax() && !$request->wantsJson()) {
                $intendedUrl = $request->fullUrl();
                return redirect()->route('login', ['intended' => $intendedUrl]);
            }
            
            // Pour les autres requêtes, redirection simple
            return redirect()->route('login');
        }

        return $next($request);
    }
}
