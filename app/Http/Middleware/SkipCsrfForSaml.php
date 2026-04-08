<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware to skip CSRF verification for SAML routes
 * Apply this BEFORE VerifyCsrfToken middleware
 */
class SkipCsrfForSaml
{
    public function handle(Request $request, Closure $next)
    {
        // Mark route for CSRF exemption if it matches SAML paths
        if ($this->shouldSkipCsrf($request)) {
            $request->attributes->set('csrf.exempt', true);
        }
        
        return $next($request);
    }

    protected function shouldSkipCsrf(Request $request): bool
    {
        $path = $request->path();
        
        return str_contains($path, 'saml2/acs')
            || str_contains($path, 'sso/acs');
    }
}

