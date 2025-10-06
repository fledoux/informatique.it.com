<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StoreIntendedUrl
{
    /**
     * Handle an incoming request.
     * Store the intended URL before redirecting to login
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si l'utilisateur n'est pas authentifié
        if (!Auth::check()) {
            // Stocker l'URL courante comme URL intended (sauf pour les requêtes AJAX ou API)
            if (!$request->ajax() && !$request->wantsJson() && $request->method() === 'GET') {
                // Ne pas stocker certaines URLs spécifiques
                $excludedRoutes = ['login', 'register', 'password.request', 'password.reset'];
                
                if (!in_array($request->route()?->getName(), $excludedRoutes)) {
                    session(['url.intended' => $request->fullUrl()]);
                }
            }
            
            // Rediriger vers la page de login
            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
