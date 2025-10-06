<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SentryContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->bound('sentry')) {
            \Sentry\configureScope(function (\Sentry\State\Scope $scope) use ($request) {
                // Informations sur l'utilisateur
                if ($user = $request->user()) {
                    $scope->setUser([
                        'id' => $user->id,
                        'email' => $user->email,
                        'name' => $user->name,
                        'company' => $user->company?->name,
                        'roles' => $user->getRoleNames()->toArray(),
                    ]);
                }
                
                // Tags utiles
                $scope->setTag('environment', app()->environment());
                $scope->setTag('version', config('app.version', '1.0.0'));
                
                // Contexte de la requête
                $scope->setContext('request', [
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                
                // Contexte de l'application
                $scope->setContext('app', [
                    'name' => config('app.name'),
                    'env' => config('app.env'),
                    'debug' => config('app.debug'),
                    'locale' => app()->getLocale(),
                ]);
            });
        }

        return $next($request);
    }
}
